import React, { useState } from 'react';
import PropTypes from 'prop-types';
import { plugin_root_url } from './../../utils/helper';

const propTypes = {};

const defaultProps = {};

export default function Documentation(props) {
	const [isOpen, setOpen] = useState(false);
	return (
		<React.Fragment>
			<div className="simple301redirects__documentation">
				<div className="simple301redirects__documentation__panel-header">
					<h4>
						<img width="25" src={plugin_root_url + 'assets/images/icon-doc.svg'} alt="doc" /> Documentation
					</h4>
					<button onClick={() => setOpen(!isOpen)}>
						<span className={`dashicons dashicons-arrow-${isOpen ? 'up' : 'down'}-alt2`}></span>
					</button>
				</div>
				<div className="simple301redirects__documentation__panel-body">
					{isOpen && (
						<div className="documentation">
							<h2>Documentation</h2>
							<h3>Simple Redirects</h3>
							<p>
								Simple redirects work similar to the format that Apache uses: the request should be relative to your WordPress root. The destination can be either a full URL to any
								page on the web, or relative to your WordPress root.
							</p>
							<h4>Example</h4>
							<ul>
								<li>
									<strong>Request:</strong> /old-page/
								</li>
								<li>
									<strong>Destination:</strong> /new-page/
								</li>
							</ul>

							<h3>Wildcards</h3>
							<p>To use wildcards, put an asterisk (*) after the folder name that you want to redirect.</p>
							<h4>Example</h4>
							<ul>
								<li>
									<strong>Request:</strong> /old-folder/*
								</li>
								<li>
									<strong>Destination:</strong> /redirect-everything-here/
								</li>
							</ul>

							<p>You can also use the asterisk in the destination to replace whatever it matched in the request if you like. Something like this:</p>
							<h4>Example</h4>
							<ul>
								<li>
									<strong>Request:</strong> /old-folder/*
								</li>
								<li>
									<strong>Destination:</strong> /some/other/folder/*
								</li>
							</ul>
							<p>Or:</p>
							<ul>
								<li>
									<strong>Request:</strong> /old-folder/*/content/
								</li>
								<li>
									<strong>Destination:</strong> /some/other/folder/*
								</li>
							</ul>
						</div>
					)}
				</div>
			</div>
		</React.Fragment>
	);
}

Documentation.propTypes = propTypes;
Documentation.defaultProps = defaultProps;
