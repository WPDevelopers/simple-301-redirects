import axios from 'axios';
export const {
	s3r_nonce,
	plugin_root_url,
	plugin_root_path,
	site_url,
	route_path,
	is_betterlinks_activated,
	hide_btl_notice,
	settings_page_url,
	wildcard,
	simple301redirects_quick_setup_step,
	usage_tracking_allowed,
} = window.Simple301Redirects;

/**
 * POST an admin-ajax action with the plugin nonce (same request shape as BetterLinks' makeRequest).
 */
export const makeRequest = (data = {}) => {
	const form_data = new FormData();
	form_data.append('security', s3r_nonce);
	Object.entries(data).forEach(([key, value]) => form_data.append(key, value));
	return axios.post(ajaxurl, form_data);
};

export const copyToClipboard = (copyText) => {
	var tempInput = document.createElement('input');
	tempInput.value = copyText;
	document.body.appendChild(tempInput);
	tempInput.select();
	document.execCommand('copy');
	document.body.removeChild(tempInput);
	return;
};

export const installPlugin = (slug) => {
	let form_data = new FormData();
	form_data.append('action', 'simple301redirects/admin/install_plugin');
	form_data.append('security', s3r_nonce);
	form_data.append('slug', slug);
	return axios.post(ajaxurl, form_data).then(
		(response) => {
			return response.data;
		},
		(error) => {
			console.log(error);
		}
	);
};

export const activePlugin = (slug) => {
	let form_data = new FormData();
	form_data.append('action', 'simple301redirects/admin/activate_plugin');
	form_data.append('security', s3r_nonce);
	form_data.append('basename', slug);
	return axios.post(ajaxurl, form_data).then(
		(response) => {
			return response.data;
		},
		(error) => {
			console.log(error);
		}
	);
};
