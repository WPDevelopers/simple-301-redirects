import React, { useState } from 'react';
import { __ } from '@wordpress/i18n';
import PropTypes from 'prop-types';
import { plugin_root_url } from './../../utils/helper';

const propTypes = {};

const defaultProps = {};

export default function Documentation(props) {
	const [isOpen, setOpen] = useState(false);
	return (
		<React.Fragment>
			<div className="simple301redirects__documentation">
				<div className="simple301redirects__documentation__panel-header" onClick={() => setOpen(!isOpen)}>
					<h4>
						<img width="25" src={plugin_root_url + 'assets/images/icon-doc.svg'} alt="doc" /> Documentation
					</h4>
					<button>
						<span className={`dashicons dashicons-arrow-${isOpen ? 'up' : 'down'}-alt2`}></span>
					</button>
				</div>
				<div className="simple301redirects__documentation__panel-body">
					{isOpen && (
						<div className="documentation">
							<h4>{__('Simple Redirects', 'simple-301-redirects')}</h4>
							<p>
								{__(
									'Simple redirects work similar to the format that Apache uses: the request should be relative to your WordPress root. The destination can be either a full URL to any page on the web, or relative to your WordPress root.',
									'simple-301-redirects'
								)}
							</p>
							<h5>{__('Example', 'simple-301-redirects')}</h5>
							<ul>
								<li>
									<strong>{__('Request: ', 'simple-301-redirects')}</strong>
									{__('/old-page/', 'simple-301-redirects')}
								</li>
								<li>
									<strong>{__('Destination: ', 'simple-301-redirects')}</strong> {__('/new-page/', 'simple-301-redirects')}
								</li>
							</ul>

							<h4>{__('Wildcards', 'simple-301-redirects')}</h4>
							<p>{__('Wildcards Redirect will redirect all files within a directory to the same filename in the redirected directory. To use wildcards, put an asterisk (*) after the folder name that you want to redirect.', 'simple-301-redirects')}</p>
							<h5>{__('Example', 'simple-301-redirects')}</h5>
							<ul>
								<li>
									<strong>{__('Request: ', 'simple-301-redirects')}</strong>
									{__('/old-folder/*', 'simple-301-redirects')}
								</li>
								<li>
									<strong>{__('Destination: ', 'simple-301-redirects')}</strong> {__('/new-folder/* ', 'simple-301-redirects')}
								</li>
							</ul>

							<p>
								{__('You can also use wildcards to redirect all the dynamic files of a certain directory to a same destination URL as well.', 'simple-301-redirects')}
							</p>
							<h5>{__('Example', 'simple-301-redirects')}</h5>
							<ul>
								<li>
									<strong>{__('Request: ', 'simple-301-redirects')}</strong>
									{__('/old-folder/*', 'simple-301-redirects')}
								</li>
								<li>
									<strong>{__('Destination: ', 'simple-301-redirects')}</strong> {__('/new-page/', 'simple-301-redirects')}
								</li>
							</ul>

							<p>
								{__('You can also use the asterisk in the destination to replace whatever it matched in the request if you like. Something like this:', 'simple-301-redirects')}
							</p>
							<h5>{__('Example', 'simple-301-redirects')}</h5>
							<ul>
								<li>
									<strong>{__('Request:', 'simple-301-redirects')}</strong>
									{__('/old-folder/*', 'simple-301-redirects')}
								</li>
								<li>
									<strong>{__('Destination:', 'simple-301-redirects')}</strong> {__('/some/other/folder/*', 'simple-301-redirects')}
								</li>
							</ul>
							<p>Or:</p>
							<ul>
								<li>
									<strong>{__('/some/other/folder/*', 'simple-301-redirects')}</strong> {__('/old-folder/*/content/', 'simple-301-redirects')}
								</li>
								<li>
									<strong>{__('Destination:', 'simple-301-redirects')}</strong> {__('/some/other/folder/*', 'simple-301-redirects')}
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
