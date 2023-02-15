<!-- 
@author x 
@title 聊天室的搜索
 -->
<template>
	<view class="chat-room-search">
		<u-header :title="$t('search')"></u-header>
		<view class="box">
			<u-search color="#ffffff" bgColor="#323256" searchIconColor="#C4C4C4" borderColor="#404065"
				placeholderColor="rgba(255,255,255,0.8)" :placeholder="$t('searchPlace')" height="80" shape="square"
				:showAction="keyword!=''" v-model="keyword" border-color @custom="search"></u-search>
			<view style="margin: 32rpx 0 16rpx;">
				<sy-tabs type="custom" size="large" :data="config" @tabChange="tabChange"></sy-tabs>
			</view>
		</view>
		<u-list @scrolltolower="scrolltolower">
			<view style="padding: 0 30rpx;">
				<template v-for="(item,key) in list">
					<chat-item :item="item"></chat-item>
				</template>
			</view>
			<view class="tips">
				<text>{{$t('没有更多')}}</text>
			</view>
		</u-list>
	</view>
</template>

<script>
	import {
		typelist,
		getChatGroupList
	} from '@/api/chat'
	export default {
		data() {
			return {
				config: [{
						key: 1,
						name: 'ecology.official'
					},
					{
						key: 3,
						name: 'ecology.business'
					},
					{
						key: 4,
						name: 'ecology.nonCommercial'
					},
					{
						key: 2,
						name: 'ecology.unofficial'
					}
				], //分类列表
				keyword: '', //关键词
				typeId: 1, //分类id
				limit: 10,
				page: 1,
				list: []
			}
		},
		onLoad() {
			this.typelist();
			this.getChatGroupList();
		},
		methods: {
			tabChange(o) {
				this.typeId = o.key;
				this.page = 1;
				this.getChatGroupList();
			},
			/* 获取聊天室类型 */
			typelist() {
				typelist().then(res => {
					this.config = res.data.map(item => {
						item.name = item.typename;
						item.key = item.id;
						return item
					})
				})
			},
			/* 滚动到底部 */
			scrolltolower() {
				this.page++;
				this.getChatGroupList();
			},
			/* 获取聊天室列表 */
			getChatGroupList() {
				this.queryChatGroupList({
					page: this.page,
					limit: this.limit,
					type_id: this.typeId,
					keyword: this.keyword
				}).then(res => {
					if (this.page == 1) {
						this.list = [];
					}
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
			/* 搜索 */
			search() {
				this.page = 1;
				this.getChatGroupList();
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

	.tips {
		padding: 32rpx 0;
		color: #D0D0D6;
		text-align: center;
		font-size: 24rpx;
	}
</style>
