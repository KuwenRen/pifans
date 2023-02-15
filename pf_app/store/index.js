import Vue from 'vue'
import Vuex from 'vuex'
Vue.use(Vuex); //vue的插件机制

import {
	getUserInfoApi
} from '@/api/user.js'

//Vuex.Store 构造器选项
const store = new Vuex.Store({
	state: { //存放状态
		country: {}, //选取的国家对象
		city: {}, //城市对象
		locale: uni.getStorageSync('locale') || 'zh-cn',
		token: uni.getStorageSync('token') || undefined,
		userInfo: uni.getStorageSync('userInfo') ? JSON.parse(uni.getStorageSync('userInfo')) : undefined
	},
	mutations: {
		updateLocale(state, v) {
			state.locale = v;
			uni.setStorageSync('locale', v);
			// #ifdef H5
			document.title = v == 'zh-cn' ? 'Pifans Club 派粉俱乐部 —— 致敬先锋，致敬派粉！' :
				'Pifans Club —— Salute Pioneers ,salute Pifans！';
			// #endif
		},
		updateToken(state, v) {
			state.token = v;
			uni.setStorageSync('token', v);
		},
		updateUserInfo(state, v) {
			state.userInfo = v;
			uni.setStorageSync('userInfo', JSON.stringify(v));
		},
	},
	actions: {
		async initUserInfo({
			commit
		}) {
			let {
				code,
				data
			} = await getUserInfoApi();
			if (code == 1) {
				commit('updateToken', data.token);
				commit('updateUserInfo', data);
			} else {
				commit('updateToken', '');
				commit('updateUserInfo', null);
			}
		}
	}
})

export default store
