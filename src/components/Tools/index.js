import React, { useState, useEffect } from 'react';
import { __ } from '@wordpress/i18n';
import PropTypes from 'prop-types';
import axios from 'axios';
import { nonce } from './../../utils/helper';

const propTypes = {};

const defaultProps = {};

export default function Tools(props) {
	const [isOpen, setOpen] = useState(false);
	const [importResponse, setImportResponse] = useState(false);
	useEffect(() => {
		const urlParams = new URLSearchParams(window.location.search);
		if (urlParams.get('import') == 'true') {
			axios.post(`${ajaxurl}?action=simple301redirects/admin/get_import_info&security=${nonce}`).then(
				(response) => {
					setImportResponse(response.data.data);
				},
				(error) => {
					console.log(error);
				}
			);
		}
	}, []);
	return (
		<React.Fragment>
			<div className="simple301redirects__panel__divider">
				<div className="simple301redirects__import">
					<div className="simple301redirects__import__head">
						<h4>Import Redirect Rules</h4>
						<p>Import your 301 Redirect Links from your Device</p>
					</div>
					<form action={'admin.php?page=301options&import=true'} method="POST" encType="multipart/form-data">
						<div role="group" className="simple301redirects-button-group" aria-labelledby="my-radio-group">
							<input type="file" id="upload_file" name="upload_file" size="25" />
							{importResponse && <p>{importResponse}</p>}
							<input type="submit" style={{marginTop: 10}} name="submit" id="submit" className="button button-primary" value={__('Import File', 'simple-301-redirects')} disabled="" />
						</div>
					</form>
				</div>
				<div className="simple301redirects__export">
					<div className="simple301redirects__export__head">
						<h4>Export Redirect Rules</h4>
						<p>Export your 301 Redirect Links Settings & Links in .CSV format</p>
					</div>
					<form action={'admin.php?page=301options&export=true'} method="POST">
						<div className="simple301redirects-button-group" >
							<button type="submit" className="btl-export-download-button">
								{__('Export File', 'simple-301-redirects')}
							</button>
							<p>Export a backup copy of your redirects.</p>
						</div>
					</form>
				</div>
			</div>
		</React.Fragment>
	);
}

Tools.propTypes = propTypes;
Tools.defaultProps = defaultProps;
