<!-- 
@author x 
@title 聊天室列表
 -->
<template>
	<view class="chat-room">
		<u-header :title="$t('群聊天')">
			<template>
				<u-icon :size="22" name="search" color="#fff" @click="goSearch"></u-icon>
				<image style="margin-left: 16rpx;width: 48rpx;" src="../../static/img/add.png" mode="widthFix"
					@click="goAdd"></image>
			</template>
		</u-header>
		<u-list @scrolltolower="scrolltolower">
			<view style="padding: 0 30rpx;">
				<template v-for="(item,key) in list">
					<chat-item :type="chat_type" :item="item"></chat-item>
				</template>
			</view>
			<view class="tips">
				<text>没有更多了~</text>
			</view>
		</u-list>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				chat_type: 1,
				page: 1,
				limit: 10,
				list: [] //列表
			}
		},
		onShow() {
			this.getChatGroupList();
		},
		methods: {
			/* 创建聊天室 */
			goAdd() {
				if (!this.$store.state.token) {
					uni.navigateTo({
						url: '/pages/my/login'
					})
					return
				}
				uni.navigateTo({
					url: '/pages/chat-room-add/chat-room-add'
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
					limit: this.limit
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
			goSearch() {
				uni.navigateTo({
					url: '/pages/chat-room-search/chat-room-search'
				})
			},
			async queryChatGroupList(data) {
				let lang = uni.getStorageSync("locale") || "zh-cn";
				data['language']=lang == "zh-cn" ? 1 : 2;
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
	.tips {
		padding: 32rpx 0;
		color: #D0D0D6;
		text-align: center;
		font-size: 24rpx;
	}
</style>
