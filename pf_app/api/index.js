import {
	http
} from './http.js';
import {
	https
} from './https.js';

export const sendCodeApi = (type, params) => {
	return http(`/common/sent${type}`, params);
}

export const getCountryListApi = () => {
	return http(`/common/country`);
}

export const getArticleListApi = (type, params) => {
	return http(`/common/${type.toLowerCase()}List`, params);
}

export const getNoahListApi = (type, params) => {
	return http(`/data/noahList`, params);
}

export const getBabbittApi = (type, params) => {
	return http(`/data/babbitt`, params);
}


export const getArticleDetailApi = (type, params) => {
	return http(`/common/${type.toLowerCase()}Detail`, params);
}

export const getWeb3DetailApi = (params) => {
	return http(`/web/getNewsDetail`, params);
}

export const sendArticleActionApi = (type, params) => {
	return http(`/user/add${type}Action`, params);
}

export const getCommentListApi = (params) => {
	return http(`/common/commentList`, params);
}

export const sendArticleCommentApi = (type, params) => {
	return http(`/user/add${type}Comment`, params);
}

export const getEcologyListApi = (params) => {
	return http('/common/ecologyList', params);
}

export const getEcologyDetailApi = (params) => {
	return http('/common/ecologyDetail', params);
}

export const getTelegramListApi = (params) => {
	return http('/common/telegraphList', params);
}

export const getPublicListApi = (params) => {
	return http('/common/notice', params);
}

export const sendLeaveMessageApi = (params) => {
	return http('/common/leaveMessage', params);
}

export const getAboutApi = () => {
	return http('/common/about');
}

export const getLabelList = (params) => {
	return http('/common/label', params);
}
export const scores = (params) => {
	return http('/common/score', params);
}
export const getbanners = (params) => {
	return http('/common/getbanner', params);
}

// 商家列表
export const getMerchantList = (params) => {
	return http('/Merchant/getMerchantList', params);
}

//商家详情
export const merchantDetail = (params) => {
	return http('/Merchant/merchantDetail', params, "get");
}

//商家编辑 
export const MerchantUpMerchant = (params) => {
	return http('/Merchant/upMerchant', params);
}

//商家认领 
export const MerchantUpClaim = (params) => {
	return http('/Merchant/upClaim', params);
}

//商家评论列表
export const MerchantMerchantLog = (params) => {
	return http('/Merchant/merchantLog', params);
}

//商家评论
export const MerchantComment = (params) => {
	return http('/Merchant/comment', params);
}


//支付类型 
export const getMerchantType = (params) => {
	return http('/Merchant/getMerchantType', params);
}

//获取共识价平均值 
export const MerchantGetConsensusPrice = (params) => {
	return http('/Merchant/getConsensusPrice', params);
}

//获取共识价平均值 
export const MerchantAddConsensusPrice = (params) => {
	return http('/Merchant/addConsensusPrice', params);
}


//获取共识价平均值 
export const GatewayserverGetProvinceList = (params) => {
	return http('/Gatewayserver/getProvinceList', params);
}


export const gatewayserver_bind = (params) => {
	return http('/gatewayserver/bind', params);
}

export const gatewayserver_send = (params) => {
	return http('/gatewayserver/send', params);
}

export const news_getExchangeList = (params) => {
	return http('/news/getExchangeList', params);
}

// 咨询搜索列表 
export const news_getInformationList = (params) => {
	return http('/news/getInformationList', params);
}

// 生态搜索列表 
export const news_getEcologyList = (params) => {
	return http('/news/getEcologyList', params);
}

// 知识搜索列表 
export const news_getKnowledgeList = (params) => {
	return http('/news/getKnowledgeList', params);
}

// 电报搜索列表 
export const news_getTelegramList = (params) => {
	return http('/news/getTelegramList', params);
}

// 排行
export const user_ranking = (params) => {
	return http('/user/ranking', params);
}


// 排行
export const getNewChatRecordList = (params) => {
	return https('v1/chatroom/get/chatTalking', params);
}

export const getChatRecordList = (params) => {
	return http('/gatewayserver/getChatRecordList', params);
}


export const newsSetCollect = (params) => {
	return http('/news/setCollect', params);
}


export const newsAddComment = (params) => {
	return http('/news/addComment', params);
}



export const newsGetCommentList = (params) => {
	return http('/news/getCommentList', params);
}


export const newsGetCollectList = (params) => {
	return http('/news/getCollectList', params);
}

export const news_cancelCollec = (params) => {
	return http('/news/cancelCollect', params);
}


export const Merchant_addMerchant = (params) => {
	return http('/Merchant/addMerchant', params);
}

export const getHotInformationList = (params) => {
	return http('/common/getHotInformationList', params);
}
