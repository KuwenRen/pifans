// const baseUrl = 'https://www.pifan.club/api/file'
const baseUrl = 'https://pifans.app/api/file'
export default {
	// 文件上传
	async uploadFile(data, type = 'image') {
		uni.showLoading();
		let res = await uni.uploadFile({
			url: baseUrl + '/uploadAvatar',
			method: 'POST',
			filePath: data.file,
			name: 'file',
			header: {
				'token': uni.getStorageSync('token')
			}
		})
		uni.hideLoading();
		return JSON.parse(res[1].data);
	}
}
