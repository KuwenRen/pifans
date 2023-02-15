import store from '@/store/index.js';
// export const baseUrl = 'https://www.pifan.club';//老服务器
export const baseUrl = 'https://pifans.app';//新服务器
// export const baseUrl = 'https://cs.pifan.club'
export const http = (url, data, method = 'POST') => {
	let token = uni.getStorageSync("token") || "";
	let lang = uni.getStorageSync("locale") || "zh-cn";
	// const baseUrl = 'https://www.pifan.club/api'
	// const baseUrl = 'https://cs.pifan.club
	const baseUrl = 'https://pifans.app/api'
	for (const k in data) {
		if (data[k] === undefined || data[k] === null) {
			delete data[k]
		}
	}
	let header = method === 'get' ? {
		token,
		language: lang == "zh-cn" ? 1 : 2
	} : {
		token,
		language: lang == "zh-cn" ? 1 : 2,
		"Content-Type": "application/x-www-form-urlencoded",
	}
	const promise = new Promise((resolve, reject) => {
		uni.request({
			url: baseUrl + url,
			// url: "http://sgl.shangguling.cn/api/web.config/getAllConfig",

			data,
			header,
			method,
			success({
				statusCode,
				data
			}) {
				if (statusCode == 200) {
					resolve(data);
					if (data.code == 0) {
						uni.showToast({
							icon: "none",
							title: data.msg
						})
					}
				} else {
					reject(data)
					uni.redirectTo({
						url: '/pages/404/404.vue'
					})
				}
			},
			fail(e) {
				console.log(e)
				// reject('网络出错');
			}
		})
	})
	return promise;
}
