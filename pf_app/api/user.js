import {
	http
} from './http.js';

export const getLocationUserApi = (params) => {
	return http('/user/getIndexInfo', params);
}

export const getUserInfoApi = () => {
	return http('/user/getUserInfo');
}

export const updateUserInfoApi = (params) => {
	return http('/user/updateUserInfo', params);
}

export const userLoginApi = (params) => {
	return http('/user/login', params);
}

export const userRegisterApi = (params) => {
	return http('/user/register', params);
}

export const userBackApi = (params) => {
	return http('/user/retrieveUserPassword', params);
}

export const userChangeApi = (params) => {
	return http('/user/updateUserPassword', params);
}
// 个人中心
export const getFansApi = (params) => {
	return http('/user/mysInviteUserList', params);
}

export const getPowerApi = (params) => {
	return http('/user/myPowerValue', params);
}

export const getRecordApi = (type, params) => {
	return http(`/user/my${type}Record`, params);
}

export const getCommentApi = (type, params) => {
	return http(`/user/my${type}Comment`, params);
}

export const delCommentApi = (type, params) => {
	return http(`/user/delete${type}Comment`, params);
}

export const getShareCodeApi = () => {
	return http('/user/shareCode');
}

/* 国家列表 */
export const getCountry = () => {
	return http('/business/getCountry', {}, 'get');
}

/* 城市列表 */
export const getProvices = () => {
	return http('/business/getProvices', {}, 'get');
}


/* 社交账号类型 */
export const getSocialAccount = (params) => {
	return http('/user/getSocialAccount', params, 'get');
}


/* 用户签到 */
export const userSign = (params) => {
	return http('/user/sign', params);
}

