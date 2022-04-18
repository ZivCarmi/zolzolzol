document.addEventListener('wpcf7mailsent', event => {
	window.dataLayer.push({
		event: 'generate_lead',
		formId: event.detail.contactFormId,
		response: event.detail.inputs
	});
});
