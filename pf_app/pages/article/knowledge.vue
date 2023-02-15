<template>
	<view>
		<u-header title=""></u-header>
		<view class="article container">
			<sy-tabs class="article-header" :value="active" type="custom" size="large" :data="config" @tabChange="tabChange"></sy-tabs>
			<sy-tabs class="article-header2" v-if="active==3" type="custom" size="large" :data="config[2].sub">
			</sy-tabs>
			<u-list class="article-warp" @scrolltolower="getListHandler">
				<view v-if="list.length !== 0" class="article-list">
					<article-card type="Knowledge" :data="item" v-for="item in list" :key="item.id" />
					<sy-loadmore style="margin-bottom: 60rpx;" v-if="status !== 'none'" :status="status" />
				</view>
				<u-empty v-else style="margin-top: 100rpx;" :text="$t('empty')" />
			</u-list>
		</view>
	</view>
</template>

<script>
	import {
		mapState
	} from 'vuex';
	import {
		getArticleListApi
	} from '@/api/index.js'
	export default {
		data() {
			return {
				active: 1,
				config: [{
						key: 1,
						name: 'article.node'
					},
					{
						key: 2,
						name: 'article.wallet'
					},
					{
						key: 3,
						name: 'article.exchange',
						sub: [{
								key: 3,
								name: '中心化交易所'
							},
							{
								key: 3,
								name: '去中心化交易所'
							},
							{
								key: 3,
								name: 'NFT交易所'
							},
						],
						url: "/pages/jiaoyisuo/jiaoyisuo"
					},
					{
						key: 4,
						name: 'article.tools'
					},

				],
				status: 'none',
				list: [],
				page: 0
			}
		},
		methods: {
			getListHandler() {
				const self = this;
				if (this.active == 1) {
					var type = 4
				}
				if (this.active == 2) {
					var type = 1
				}
				if (this.active == 3) {
					var type = 2
				}
				if (this.active == 4) {
					var type = 3
				}
				let {
					list,
					page
				} = this;
				(this.status !== 'nomore') && getArticleListApi('Knowledge', {
					type: type,
					page: page + 1,
					listRows: 12
				}).then(({
					code,
					data: {
						data,
						hot_data
					},
					msg
				}) => {
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
			tabChange(item) {
				this.active = item.key;
				this.init();
			},
			init() {
				this.status = 'none';
				this.page = 0;
				this.list = [];
				this.getListHandler();
			}
		},
		computed: {
			...mapState({
				locale: state => state.locale
			}),
			localeIndex() {
				return this.locale == 'zh-cn' ? 1 : 2;
			},
		},
		created() {
			
		},
		onLoad(e) {
			if (e.type) {
				let type = e.type
				if (type == 3) {
					this.active = 2
				} else {
					this.active = 4
				}
			}
			this.init()
		}
	}
</script>

<style lang="scss" scoped>
	.article-header2 {
		margin-top: 20rpx;
	}

	.article {
		//padding-top: calc(64rpx + var(--status-bar-height));
		padding-left: 40rpx;
		padding-right: 40rpx;
		padding-bottom: 50px;
		box-sizing: border-box;

		.article-warp {
			margin-top: 32rpx;
			height: calc(100vh - 50px - 154rpx) !important;
		}

		.article-list {
			display: flex;
			flex-direction: column;
		}
	}
</style>
