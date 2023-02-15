import Vue from 'vue'
import App from './App.vue'
// 引入store
import store from './store'

// 引入全局uView
import uView from '@/uni_modules/uview-ui'
// 引入i18n
import VueI18n from 'vue-i18n'
import { i18nOpt } from './i18n/index.js'
Vue.config.productionTip = false
App.mpType = 'app'
Vue.use(VueI18n)

// #ifdef H5
	window._AMapSecurityConfig = {
	  securityJsCode: '4671c9eabe954a16b225f0cefb8e71ec',
	}
// #endif

Vue.use(uView)
const app = new Vue({
	i18n: new VueI18n(i18nOpt),
	store,
	...App
})
app.$mount()

