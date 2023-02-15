import {
	http
} from './http.js';

/* 分类列表 */
export const typelist = (params) => {
	return http('/business/getBusinessType', params);
}

/* 商家列表 */
export const getMechantList = (params) => {
	return http('/business/getMechantList', params, 'get');
}

/* 商家详情 */
export const mechantInfo = (params) => {
	return http('/business/mechantInfo', params);
}

/* 商家支付方式 */
export const getMerchantType = (params) => {
	return http('/business/getMerchantType', params, 'get');
}

/* 商家入驻 */
export const createMechant = (params) => {
	return http('/business/createMechant', params);
}
