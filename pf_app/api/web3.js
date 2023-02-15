import {
	http
} from './http.js';

/* 分类列表 */
export const typelist = (params) => {
	return http('/web/typelist', params, 'get');
}

/* 数据列表 */
export const getWebList = (params) => {
	return http('/web/getWebList', params);
}
