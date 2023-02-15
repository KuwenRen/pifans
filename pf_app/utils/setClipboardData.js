export default function setClipboardData(data) {
	return new Promise((success, fail) => {
		// #ifndef H5
		uni.setClipboardData({
			data,
			success,
			fail
		})
		// #endif

		// #ifdef H5
		const textarea = document.createElement('textarea')
		textarea.value = data
		textarea.readOnly = true
		document.body.appendChild(textarea)
		textarea.select()
		textarea.setSelectionRange(0, data.length)
		document.execCommand('copy')
		textarea.remove()
		success(data)
		// #endif
	})
}
