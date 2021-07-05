import axios from 'axios';
export const {
	s3r_nonce,
	plugin_root_url,
	plugin_root_path,
	site_url,
	route_path,
	is_betterlinks_activated,
	hide_btl_notice,
} = window.Simple301Redirects;

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
