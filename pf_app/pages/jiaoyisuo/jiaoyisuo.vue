<template>
	<view class="">
		<u-navbar :title="$t('交易所')" bgColor="#14142B" leftIconColor="#F585FF" autoBack />
		<view class="container">
			<sy-tabs class="article-header" type="custom" size="large" :data="config" @tabChange="tabChange"></sy-tabs>
			<u-list class="article-warp"  @scrolltolower="getListHandler">
				<view v-if="list.length !== 0" class="article-list">
					<article-card type="Knowledge" :data="item" v-for="item in list" :key="item.id" />
					<sy-loadmore style="margin-bottom: 90rpx;" v-if="status !== 'none'" :status="status" />
				</view>
				<u-empty v-else style="margin-top: 100rpx;" :text="$t('empty')"/>
			</u-list>
		</view>
	</view>
</template>

<script>
	import { mapState } from 'vuex';
	import { getArticleListApi,news_getExchangeList } from '@/api/index.js'
	export default {
		data() {
			return {
				status: 'none',
				list: [],
				page: 0,
				active: 5,
				config: [
					{ key: 5, name: '中心化交易所' },
					{ key: 6, name: '去中心化交易所' },
					{ key: 7, name: 'NFT交易所' },
				],
			}
		},
		onLoad() {
			this.init()
		},
		methods: {
			tabChange(item) {
				this.active = item.key;
				console.log(this.active)
				this.init();
			},
			init() {
				this.status = 'none';
				this.page = 0;
				this.list = [];
				this.getListHandler();
			},
			getListHandler() {
				const self = this;
				let { list, page } = this;
				(this.status !== 'nomore') && news_getExchangeList({ type: this.active, page: page + 1, listRows: 12 }).then(({code, data, msg}) => {
					if (code == 1) {
						if (data.data.length == 0) {
							self.status = 'nomore';
						} else {
							self.page = page + 1;
							self.list = list.concat(data.data);
						}
					} else {
						uni.$u.toast(msg);
					}
				})
			},
		}
	}
</script>

<style scoped>
.container{
	min-height: calc(100vh - 44px) !important;
	height: calc(100vh - 44px) !important;
	margin-top: -1px;
	padding: 0 30rpx;
	overflow: hidden;
	padding-top: 30rpx;
}
.article-header{
		margin-top: 30rpx;
	}
	.article-warp {
		margin-top: 32rpx;
		height: calc(100vh - 50px - 154rpx)!important;
		.article-warp {
			margin-top: 32rpx;
			height: calc(100vh - 50px - 154rpx)!important;
		}
		.article-list {
			display: flex;
			flex-direction: column;
		}
	}
</style>
