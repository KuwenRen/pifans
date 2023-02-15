<template>
	<view class="ecology container">
		<u-navbar :title="$t('ecology.index')" bgColor="#14142B" leftIconColor="#F585FF" autoBack fixed placeholder />
		<view class="warp"></view>
		<sy-tabs type="custom" size="large" v-model="active" :data="config" @tabChange="tabChange"></sy-tabs>
		<u-list class="ecology-warp" @scrolltolower="getListHandler">
			<template v-if="list.length !== 0">
				<view class="ecology-list">
					<view class="ecology-list__item" v-for="(item, index) in list" :key="index">
						<encology-item :data="item" />
					</view>
				</view>
				<sy-loadmore style="margin-bottom: 60rpx;" v-if="status !== 'none'" :status="status" />
			</template>
			<u-empty v-else style="margin-top: 100rpx;" :text="$t('empty')" />
		</u-list>
	</view>
</template>

<script>
	import {
		mapState
	} from 'vuex';
	import {
		getEcologyListApi
	} from '@/api/index.js'
	export default {
		data() {
			return {
				active: 1,
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
					// {
					// 	key: 2,
					// 	name: 'ecology.unofficial'
					// }
				],
				status: 'none',
				list: [],
				page: 0
			}
		},
		methods: {
			getListHandler() {
				let {
					list,
					page
				} = this;
				(this.status !== 'nomore') && getEcologyListApi({
					type: this.active,
					page: page + 1,
					listRows: 20
				}).then(({
					code,
					data: {
						data,
						total
					},
					msg
				}) => {
					if (code == 1) {
						if (data.length == 0) {
							this.status = 'nomore';
						} else {
							this.page = page + 1;
							this.list = list.concat(data);
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
			this.init();
		},
	}
</script>

<style lang="scss" scoped>
	.ecology {
		//padding-top: calc(64rpx + var(--status-bar-height));
		padding-top: 88upx;
		padding-left: 40rpx;
		padding-right: 40rpx;
		padding-bottom: 50px;
		box-sizing: border-box;

		.warp {
			padding-top: 70rpx;
		}

		.ecology-warp {
			margin-top: 32rpx;
			// height: calc(100vh - 50px - 154rpx) !important;
			height: calc(90vh - 48px - 70rpx) !important;
		}

		.ecology-list {
			margin: -16rpx;
			display: flex;
			flex-wrap: wrap;

			.ecology-list__item {
				flex: 0 0 33.33333333%;
				max-width: 33.33333333%;
				padding: 16rpx;
				box-sizing: border-box;
			}
		}


	}
</style>
