import {
	http
} from './http.js';


/* 聊天室类型 */
export const typelist = (params) => {
	return http('/chatroom/typelist', params, 'get');
}


/* 聊天室列表 */
export const getChatGroupList = (params) => {
	return http('/chatroom/get/chatList', params, 'get');
}

/* 修改群聊名称 */
export const updChatGroupName = (params) => {
	return http('/chatroom/updChatGroupName', params);
}

/* 修改群聊-个人信息 */
export const updChatUserInfo = (params) => {
	return http('/chatroom/updChatUserInfo', params);
}

/* 聊天室信息 */
export const chatInfo = (params) => {
	return http('/chatroom/chatInfo', params, 'get');
}


/* 聊天室-成员信息 */
export const getMembers = (params) => {
	return http('/chatroom/getMembers', params,'get');
}
/**
 * 更换群主
 */
export const changeMaster = (params) => {
	return http('/chatroom/changeMaster', params,'get');
}
//获取聊天室用户信息
export const getChatUserInfo = (params) => {
	return http('/chatroom/getChatUserInfo', params,'get');
}
//修改聊天室用户信息
export const editChatUserInfo = (params) => {
	return http('/chatroom/editChatUserInfo', params,'get');
}

/* 退出聊天室 */
export const exitChatroom = (params) => {
	return http('/chatroom/exitChatroom', params, 'get');
}


/* 获取聊天内容 */
export const chatlist = (params) => {
	return http('/chatroom/get/chat/list', params);
}


/* 新增聊天室/群聊 */
export const addChatGroup = (params) => {
	return http('/chatroom/addChatGroup', params);
}
