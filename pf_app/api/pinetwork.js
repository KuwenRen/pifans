import {
	http
} from './http.js';

/* 分类列表 */
export const typelist = (params) => {
	return http('/pinetwork/typelist', params);
}

/* 数据列表 */
export const getPinetworkList = (params) => {
	return http('/pinetwork/getPinetworkList', params);
}
