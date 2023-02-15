<!-- 
@author x 
 -->
<!-- 搜索 -->
<template>
	<view class="search">
		<u-header></u-header>
		<view class="box">
			<!-- <u-search color="#ffffff" bgColor="#323256" searchIconColor="#C4C4C4" borderColor="#404065"
				placeholderColor="rgba(255,255,255,0.8)" :placeholder="$t('searchPlace')" height="80" shape="square"
				:showAction="false" v-model="searchWords" border-color></u-search> -->
				
				<u-search color="#ffffff" bgColor="#323256" searchIconColor="#C4C4C4" borderColor="#404065"
					placeholderColor="rgba(255,255,255,0.8)" :placeholder="$t('searchPlace')" height="80" shape="square"
					:showAction="searchWords!=''" v-model="searchWords" border-color @custom="search"></u-search>
			<view style="margin: 32rpx 0 16rpx;">
				<sy-tabs type="custom" size="large" :data="config" @tabChange="tabChange"></sy-tabs>
			</view>
		</view>
		<u-list @scrolltolower="scrolltolower">
			<view class="list">
				<template v-if="tabtype==1">
					<web3-item @Detail="getDetail" v-for="(item,key) in list" :key="key" :item="item"></web3-item>
				</template>
				<template v-else-if="tabtype==2">
					<news-item v-for="(item,key) in list" :key="key" :item="item"></news-item>
				</template>
				<template v-else-if="tabtype==3">
					<chat-item v-for="(item,key) in list" :key="key" :item="item"></chat-item>
				</template>
			</view>
			<view class="tips">
				<text>没有更多了~</text>
			</view>
		</u-list>
	</view>
</template>

<script>
	import {
		getWebList
	} from '@/api/web3'
	import {
		getPinetworkList
	} from '@/api/pinetwork'
	import {
		getArticleListApi
	} from '@/api/index.js'
	export default {
		data() {
			return {
				searchWords: '',
				list: [],
				page: 1,
				limit: 10,
				config: [{
						key: 1,
						name: 'Web3'
					},
					{
						key: 2,
						name: 'Pinetwork'
					},
					{
						key: 3,
						name: 'chart'
					}
				],
				tabtype: 1
			}
		},
		onLoad() {
			this.getWebList();
		},
		methods: {
			search() {
				this.page = 1;
				if (this.tabtype == 1) {
					this.getWebList();
				} else if (this.tabtype == 2) {
					// this.getPinetworkList();
					this.getArticleListApi();
				} else if (this.tabtype == 3) {
					this.getChatGroupList();
				}
			},
			getDetail(e) {
				uni.navigateTo({
					url: '/pages/web3/detail?id=' + e + '&type=0'
				})
			},
			tabChange(o) {
				this.tabtype = o.key;
				this.page = 1;
				if (this.tabtype == 1) {
					this.getWebList();
				} else if (this.tabtype == 2) {
					// this.getPinetworkList();
					this.getArticleListApi();
				} else if (this.tabtype == 3) {
					this.getChatGroupList();
				}
			},
			/* 滚动到底部 */
			scrolltolower() {
				this.page++;
				if (this.tabtype == 1) {
					this.getWebList();
				} else if (this.tabtype == 2) {
					// this.getPinetworkList();
					this.getArticleListApi();
				} else if (this.tabtype == 3) {
					this.getChatGroupList();
				}
			},
			//pinetwork列表
			getArticleListApi(){
				getArticleListApi('Information', {
					page: this.page,
					listRows: this.limit,
					keyword: this.searchWords
				}).then(res => {
					uni.hideLoading();
					this.list = res.data.list;
				})
			},
			/* 数据列表 */
			getWebList() {
				getWebList({
					page: this.page,
					limit: this.limit,
					keyword: this.searchWords
				}).then(res => {
					if (res.data.list.length <= 0) {
						return
					}
					if (this.page == 1) {
						this.list = res.data.list;
					} else {
						this.list = this.list.concat(res.data.list);
					}
				})
			},
			/* 数据列表 */
			getPinetworkList() {
				getPinetworkList({
					page: this.page,
					limit: this.limit,
					keyword: this.searchWords
				}).then(res => {
					if (res.data.list.length <= 0) {
						return
					}
					if (this.page == 1) {
						this.list = res.data.list;
					} else {
						this.list = this.list.concat(res.data.list);
					}
				})
			},
			/* 获取聊天室列表 */
			getChatGroupList() {
				this.queryChatGroupList({
					page: this.page,
					limit: this.limit,
					keyword: this.searchWords
				}).then(res => {
					if (res.data.list.length <= 0) {
						return
					}
					if (this.page == 1) {
						this.list = res.data.list;
					} else {
						this.list = this.list.concat(res.data.list);
					}
				})
			},
			async queryChatGroupList(data) {
				let res = await uni.request({
					url: 'https://chat.pifans.app/v1/chatroom/get/chatList',
					data: data,
				})
				return res[1].data
			}
		}
	}
</script>

<style lang="scss" scoped>
	.box {
		position: sticky;
		z-index: 99;
		top: calc(90rpx + var(--status-bar-height));
		padding: 0 30rpx;
		background-color: #050523;
	}

	.list {
		padding: 0 30rpx;
		overflow: hidden;
	}

	.tips {
		padding: 32rpx 0;
		color: #D0D0D6;
		text-align: center;
		font-size: 24rpx;
	}
</style>
