/**
 * CSS Editor React Component
 *
 * Advanced CSS editor using CodeMirror 6 for custom slider styling.
 *
 * @package
 */

import { useState, useEffect, useRef } from '@wordpress/element';
import { Button } from '@wordpress/components';
import { EditorView, basicSetup } from 'codemirror';
import { css } from '@codemirror/lang-css';
import { oneDark } from '@codemirror/theme-one-dark';

const CSSEditor = ( { initialValue = '', onChange } ) => {
	const editorRef = useRef( null );
	const viewRef = useRef( null );
	const [ hasChanges, setHasChanges ] = useState( false );

	useEffect( () => {
		if ( ! editorRef.current ) {
			return;
		}

		// Create CodeMirror editor
		const view = new EditorView( {
			doc: initialValue,
			extensions: [
				basicSetup,
				css(),
				oneDark,
				EditorView.updateListener.of( ( update ) => {
					if ( update.docChanged ) {
						const newValue = update.state.doc.toString();
						onChange( newValue );
						setHasChanges( true );
					}
				} ),
			],
			parent: editorRef.current,
		} );

		viewRef.current = view;

		// Cleanup on unmount
		return () => {
			view.destroy();
		};
		// eslint-disable-next-line react-hooks/exhaustive-deps
	}, [] );

	const handleReset = () => {
		if (
			// eslint-disable-next-line no-alert
			window.confirm(
				'Are you sure you want to reset the custom CSS? This cannot be undone.'
			)
		) {
			if ( viewRef.current ) {
				viewRef.current.dispatch( {
					changes: {
						from: 0,
						to: viewRef.current.state.doc.length,
						insert: '',
					},
				} );
			}
			onChange( '' );
			setHasChanges( false );
		}
	};

	return (
		<div className="wc-ps-css-editor">
			<div className="wc-ps-css-editor-toolbar">
				<p className="description">
					Add custom CSS to style your slider. Changes will be applied
					only to this slider instance.
				</p>
				{ hasChanges && (
					<span className="wc-ps-unsaved-changes">
						Unsaved changes
					</span>
				) }
			</div>

			<div ref={ editorRef } className="wc-ps-codemirror-wrapper" />

			<div className="wc-ps-css-editor-actions">
				<Button
					isDestructive
					variant="tertiary"
					onClick={ handleReset }
					disabled={ ! hasChanges && ! initialValue }
				>
					Reset CSS
				</Button>
				<p className="description">
					Tip: Use <code>.wc-ps-slider-{ '{{ID}}' }</code> to target
					this specific slider
				</p>
			</div>
		</div>
	);
};

export default CSSEditor;
