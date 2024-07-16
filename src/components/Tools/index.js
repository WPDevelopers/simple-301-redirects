import React, { useState, useEffect } from 'react';
import { __ } from '@wordpress/i18n';
import axios from 'axios';
import { s3r_nonce } from './../../utils/helper';

export default function Tools(props) {
	const [importResponse, setImportResponse] = useState(false);
	useEffect(() => {
		const urlParams = new URLSearchParams(window.location.search);
		if (urlParams.get('isImport') == 'true') {
			axios.post(`${ajaxurl}?action=simple301redirects/admin/get_import_info&security=${s3r_nonce}`).then(
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
						<h4>{__('Import Redirect Rules', 'simple-301-redirects')}</h4>
						<p>{__('Import your 301 Redirect Links from your Device', 'simple-301-redirects')}</p>
					</div>
					<form action={'admin.php?page=301options&isImport=true'} method="POST" encType="multipart/form-data">
						<div role="group" className="simple301redirects-button-group" aria-labelledby="my-radio-group">
							<input type="hidden" name="import" value={true} />
							<input type="hidden" name="security" value={s3r_nonce} />
							<input type="file" id="upload_file" name="upload_file" size="25" />
							<input
								type="submit"
								name="submit"
								id="submit"
								className="button button-primary"
								style={{ marginTop: 10 }}
								value={__('Import File', 'simple-301-redirects')}
								disabled=""
							/>
						</div>
						{importResponse && (
							<p>
								<strong>{importResponse.replace(/"|"/g, '')}</strong>
							</p>
						)}
					</form>
				</div>
				<div className="simple301redirects__export">
					<div className="simple301redirects__export__head">
						<h4>{__('Export Redirect Rules', 'simple-301-redirects')}</h4>
						<p>{__('Export your 301 Redirect Links in .csv format', 'simple-301-redirects')}</p>
					</div>
					<form action={'admin.php?page=301options'} method="POST">
						<input type="hidden" name="export" value={true} />
						<input type="hidden" name="security" value={s3r_nonce} />
						<div className="simple301redirects-button-group">
							<button type="submit" className="btl-export-download-button">
								{__('Export File', 'simple-301-redirects')}
							</button>
						</div>
					</form>
				</div>
			</div>
		</React.Fragment>
	);
}