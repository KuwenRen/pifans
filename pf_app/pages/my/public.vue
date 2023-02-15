<template>
	<u-list class="public container" @scrolltolower="getListHandler">
		<u-navbar :title="$t('menu.public')" bgColor="#14142B" leftIconColor="#F585FF" autoBack fixed placeholder />
		<view class="warp">
			<sy-tabs type="custom" size="large" v-model="active" :data="config" @tabChange="tabChange"></sy-tabs>
			<view class="public-list" v-if="list.length !== 0">
				<view class="public-item" v-for="item in list" :key="item.id">
					<u-text lines="1" :text="item['title'+localeIndex]" color="#FFFFFF" size="36rpx" lineHeight="44rpx" bold></u-text>
					<u-text margin="16rpx 0 20rpx" :text="item['content'+localeIndex]" color="rgba(255, 255, 255, 0.8)" size="26rpx" lineHeight="36rpx"></u-text>
					<u-text lines="1" mode="date" format="yyyy-mm-dd hh:MM" :text="item.createtime" color="rgba(255, 255, 255, 0.6)" size="24rpx" lineHeight="32rpx"></u-text>
				</view>
				<sy-loadmore v-if="status !== 'none'" :status="status" />
			</view>
			<u-empty v-else style="margin-top: 100rpx;" :text="$t('empty')"/>
		</view>
	</u-list>
</template>

<script>
	import { mapState } from 'vuex';
	import { getPublicListApi } from '@/api/index.js';
	export default {
		data() {
			return {
				active: 1,
				config: [
					{ key: 1, name: 'menu.public' },
					// { key: 2, name: 'menu.quick' },
					// { key: 3, name: '金色财经' },
					// { key: 4, name: '巴比特快讯' },
				],
				status: 'none',
				count: 0,
				list: [],
				page: 0
			}
		},
		methods: {
			getListHandler() {
				let { search, list, page } = this;
				(this.status !== 'nomore') && getPublicListApi({ notice_type: this.active, page: page + 1, listRows: 12 }).then(({code, data:{ data, total }, msg}) => {
					if (code == 1) {
						if (data.length == 0) {
							this.status = 'nomore';
						} else {
							this.page = page + 1;
							this.list = list.concat(data);
						}
						this.count = total;
					} else {
						uni.$u.toast(msg);
					}
				})
			},
			init() {
				this.status = 'none';
				this.page = 0;
				this.list = [];
				this.getListHandler();
			},
			tabChange({key}) {
				this.active = key;
				this.init();
			}
		},
		computed: {
			...mapState({
				locale: state => state.locale
			}),
			localeIndex() {
				return this.locale == 'zh-cn'? 1 : 2;
			}
		},
		onLoad({active}) {
			// if (active) this.active = active;
			this.init();
		}
	}
</script>

<style lang="scss" scoped>
	.public {
		.warp {
			padding-top: 170rpx;
			.public-item {
				margin-top: 32rpx;
				padding: 32rpx;
				background: #191932;
				border: 1rpx solid rgba(255,255,255,0.3);
				border-radius: 8rpx;
			}
		}
	}
</style>
