<!-- 
@author x
@title 首页
 -->
<template>
	<view class="home container">
		<view class="home-header">
			<image class="logo" src="/static/img/home-logo.png" mode="" />
			<i18nSwitch />
		</view>
		<!-- <view class="home-search">
			<u-search color="#ffffff" bgColor="#323256" searchIconColor="#C4C4C4" borderColor="#404065"
				placeholderColor="rgba(255,255,255,0.8)" :placeholder="$t('searchPlace')" height="80" shape="square"
				:showAction="false" v-model="searchWords" border-color @click="searchHnadler" disabled></u-search>
			 <u-button customStyle="margin-left: 16rpx;width: 220rpx;height:42px" :text="$t('ecology.download')"
                      color="linear-gradient(to right, #9041FF, #E323FF)" @click="downloadHandler"></u-button>
		</view> -->
		<view class="banner">
			<swiper :indicator-dots="indicatorDots" :autoplay="autoplay" circular="true" :interval="interval"
				:duration="duration">
				<swiper-item v-for="(item,index) in banner" :key="index" @click="toDetails(item.url)">
					<view class="swiper-item bg-gray radius">
						<image v-if="localeIndex==1" :src="imgUlr+item.images" class="radius"></image>
						<image v-else :src="imgUlr+item.en_image" class="radius"></image>
					</view>
				</swiper-item>
			</swiper>
		</view>


		<!-- <image class="home-banner" src="../../static/img/home-banner.png" mode=""></image> -->
		<!-- <view class="home-quick">
			<view class="home-quick__icon">
				{{$t('menu.quick')}}
			</view>
			<swiper class="home-quick__swiper" :indicator-dots="false" :interval="3000" :duration="1000" vertical
				autoplay circular>
				<swiper-item v-for="(item, index) in public" :key="index">
					<navigator class="public-item" url="/pages/my/public?active=2">
						<u-text margin="0 22rpx" lines="1" :text="item['title'+localeIndex]"
							color="rgba(255,255,255,0.8)" size="26rpx" lineHeight="36rpx" blod></u-text>
						<u-icon name="arrow-right" color="#C4C4C4" size="28rpx"></u-icon>
					</navigator>
				</swiper-item>
			</swiper>
		</view> -->
		<!-- <home-card :title="$t('menu.map')" more="custom" moreText="go" @moreClick="changeTab('map')"> -->
		<view class="sy-map-line"></view>
		<!-- #ifdef H5 -->
		<sy-map @mapClick="goMap" el="homeMap" type="simple" height="400rpx"></sy-map>
		<!-- #endif -->

		<!-- #ifdef APP-PLUS -->
		<sy-map-app el="homeMap" type="simple" height="400rpx"></sy-map-app>
		<!-- #endif -->
		<view class="sy-map-line"></view>
		<!-- 商家入驻 -->
		<view class="shop-join" @click="shopjoin">
			<view class="label-content">
				<image class="icon" src="../../static/news/2.png"></image>
				<text class="label-tt">{{$t('商家列表')}}</text>
				<text class="label-tt1" v-if="localeIndex==1">点击查看商家列表</text>
				<!-- <text class="label-tt2" v-else>Click to submit store apply</text> -->
				<text class="label-tt2" v-else>Click to get stores list</text>
				<image class="icon1" src="../../static/news/5.png" mode=""></image>
			</view>
		</view>
		<!-- 分类 -->
		<view class="category">
			<view v-for="(item,key) in categorylist" :key="key" class="item" @click="goPath(item)">
				<image class="icon" :src="item.icon" mode="widthFix"></image>
				<image class="bg-icon" :src="item.bg_icon"></image>
				<text class="item-title" v-if="localeIndex==1">{{item.name1}}</text>
				<text class="item-title" v-else>{{item.name2}}</text>
			</view>
		</view>

		<!-- </home-card> -->
		<!-- <home-card :title="$t('menu.information')" more="custom" @moreClick="changeIndex('information')">
			<sy-tabs :data="informationTabs">
				<template v-slot:default="slotProps">
					<article-item :key="articleItemKey" type="Information" :data="slotProps.item"></article-item>
				</template>
			</sy-tabs>
		</home-card>
		<home-card :title="$t('menu.ecology')" more="custom" @moreClick="changeTab('ecology')">
			<sy-tabs :data="ecologyTabs" grid>
				<template v-slot:default="slotProps">
					<encology-item :data="slotProps.item" />
				</template>
			</sy-tabs>
		</home-card>
		<home-card :title="$t('menu.knowledge')" more="custom" @moreClick="changeTab('knowledge')">
			<sy-tabs :data="knowledgeTabs">
				<template v-slot:default="slotProps">
					<article-item type="Knowledge" :data="slotProps.item"></article-item>
				</template>
			</sy-tabs>
		</home-card>
		<home-card :title="$t('menu.telegram')" more="" @moreClick="changeIndex('telegram')">
			<telegram-item v-for="(item,index) in telegramList" :key="index" :data="item" />
		</home-card> -->
	</view>
</template>

<script>
	import {
		mapState
	} from 'vuex';
	import {
		getPublicListApi,
		getArticleListApi,
		getEcologyListApi,
		getTelegramListApi,
		getbanners,
		getNoahListApi,
		getBabbittApi
	} from '../../api/index.js';
	import {
		baseUrl
	} from '../../api/http.js'

	export default {
		name: "home",
		data() {
			return {
				categorylist: [{
					icon: '/static/home/web3.png',
					name1: 'Web3科普',
					name2: 'Web3',
					path: '/pages/web3/web3?showBack=true',
					bg_icon: '/static/home/home1.png'
				}, {
					icon: '/static/home/chat.png',
					name1: '聊天室',
					name2: 'Chat room',
					// path: '/pages/chat-room/chat-room',
					path: '/pages/msgList/msgList',
					bg_icon: '/static/home/home2.png'
				}, {
					icon: '/static/home/pinetwork.png',
					name1: 'Pinetwork',
					name2: 'Pinetwork',
					path: '/pages/pinetwork/pinetwork',
					bg_icon: '/static/home/home3.png'
				}, {
					icon: '/static/home/hot.png',
					name1: '加密商家',
					name2: 'Hot-projects',
					// path: '/pages/telegram/telegram'
					// path: '/pages/hot/information'
					path: '/pages/shop-search/shop-search',
					bg_icon: '/static/home/home4.png'
				}], //分类列表
				articleItemKey: new Date().getTime(),
				banner: [],
				indicatorDots: true,
				vertical: false,
				autoplay: true,
				interval: 3000,
				duration: 500,
				searchWords: '',
				public: [],
				informationTabs: [{
						key: 1,
						name: 'article.pinetwork',
						list: []
					},
					{
						key: 2,
						name: 'article.cryptocurrency',
						list: []
					},
					{
						key: 3,
						name: 'Pi数据',
						url: "/pages/shuju/shuju"
					},
					{
						key: 4,
						name: '免费',
						list: []
					},
				],
				ecologyTabs: [{
						key: 1,
						name: 'ecology.official',
						list: []
					},
					{
						key: 3,
						name: 'ecology.business',
						list: []
					},
					{
						key: 4,
						name: 'ecology.nonCommercial',
						list: []
					},
					{
						key: 2,
						name: 'ecology.unofficial',
						list: []
					},
				],
				knowledgeTabs: [{
						key: 1,
						name: 'article.node',
						list: []
					},
					{
						key: 2,
						name: 'article.wallet',
						list: []
					},
					{
						key: 3,
						name: 'article.exchange',
						list: []
					},
					{
						key: 4,
						name: 'article.tools',
						list: []
					},
				],
				telegramList: [],
				imgUlr: baseUrl
			};
		},
		methods: {
			goMap() {
				this.$emit('changeTab', 'map')
			},
			goPath(item) {
				if (item.name == 'Pinetwork' || item.name == '聊天室') {
					// 判断是否登录
					if (!this.$store.state.token) {
						uni.navigateTo({
							url: '/pages/my/login'
						})
						return
					}
				}
				uni.navigateTo({
					url: item.path
				})
			},
			/* 商家入住 */
			shopjoin() {
				// 判断是否登录
				// if (!this.$store.state.token) {
				// 	uni.navigateTo({
				// 		url: '/pages/my/login'
				// 	})
				// 	return
				// }
				// uni.navigateTo({
				// 	url: '/pages/shopjoin/shopjoin'
				// })
				uni.navigateTo({
					url: '/pages/shop-search/shop-search'
				})
			},
			searchHnadler() {
				// 跳转到搜索页
				uni.navigateTo({
					url: '/pages/search/search'
				})
				// this.$emit('changeTab', 'telegram')
			},
			downloadHandler() {
				// #ifdef H5
				location.href = 'http://pifans.app/uploads/pifans.apk';
				// #endif
				// #ifdef APP-PLUS
				plus.runtime.openURL('http://pifans.app/uploads/pifans.apk');
				// #endif
			},
			initPublic() {
				getPublicListApi({
					page: 1,
					listRows: 5
				}).then(({
					code,
					data: {
						data
					},
					msg
				}) => {
					this.public = data || [];
				})
				getbanners().then(({
					code,
					data,
					msg
				}) => {

					this.banner = data || [];
				})
			},
			initInformation() {
				this.informationTabs.forEach(item => {
					const params = {
						type: item.key,
						page: 1,
						listRows: 3
					}

					let addForm = []
					if (item.key === 2) {
						this.$nextTick(async () => {
							const NoahLis = await getNoahListApi('information', params);
							const Babbitt = await getBabbittApi('information', params);
							const {
								data: {
									list = []
								}
							} = NoahLis

							list.forEach((l) => {
								addForm.push({
									title1: l.content_prefix,
									title2: l.content,
									createtime: l.created_at
								})
							})
							const {
								data = []
							} = Babbitt
							data.forEach((l) => {
								addForm.push({
									title1: l.title,
									title2: l.content,
									createtime: l.date_time
								})
							})

							item.list = addForm;
							this.articleItemKey = new Date().getTime()
						}, this)

					} else {
						getArticleListApi('information', params).then(({
							code,
							data: {
								data,
								hot_data
							},
							msg
						}) => {
							let {
								data: list
							} = data;
							item.list = list || [];

						})
					}
				})
			},

			initEcology() {
				this.ecologyTabs.forEach(item => {
					const params = {
						type: item.key,
						page: 1,
						listRows: 6
					}
					getEcologyListApi(params).then(({
						code,
						data: {
							data,
							total
						},
						msg
					}) => {
						item.list = data || [];
					})
				})
			},
			initKnowledge() {
				this.knowledgeTabs.forEach(item => {
					if (item.key == 1) {
						var type = 4
					}
					if (item.key == 2) {
						var type = 1
					}
					if (item.key == 3) {
						var type = 2
					}
					if (item.key == 4) {
						var type = 3
					}
					const params = {
						type: type,
						page: 1,
						listRows: 3
					}
					getArticleListApi('knowledge', params).then(({
						code,
						data: {
							data,
							hot_data
						},
						msg
					}) => {
						let {
							data: list
						} = data;
						item.list = list || [];
					})
				})
			},
			initTelegram() {
				getTelegramListApi({
					search: '',
					page: 1,
					listRows: 6
				}).then(({
					code,
					data: {
						data: {
							data,
							total
						},
						hot_data
					},
					msg
				}) => {
					this.telegramList = data || [];
				})
			},
			changeTab(k) {
				this.$emit('changeTab', k)
			},
			changeIndex(k) {
				this.$emit('changeIndex', k)
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
			this.initPublic();
			this.initEcology();
			this.initTelegram();
			this.initKnowledge();
			this.initInformation();
			// this.initInfornoahList();
			// this.initInforbabbitt();
		}
	}
</script>

<style lang="scss" scoped>
	.sy-map-line {
		margin-top: 32rpx;
	}

	// 分类
	.category {
		margin: 32rpx 0 48rpx;
		display: grid;
		grid-template-columns: repeat(2, 50fr);
		grid-gap: 32rpx;
		font-size: 32rpx;

		.item {
			display: flex;
			flex-direction: column;
			color: #D0D0D6;
			justify-content: center;
			align-items: center;
			background-color: #14142B;
			border-radius: 2px;
			height: 260rpx;
			position: relative;
			

			.bg-icon {
				width: 100%;
				height: 100%;
				position: absolute;
				z-index: 1;
			}

			.icon {
				width: 88rpx;
				z-index: 2;
				margin-bottom: 32rpx;
			}

			.item-title {
				z-index: 2;
				color: #fff;
			}
		}
	}

	// 商家入驻
	.shop-join {
		margin-bottom: 40rpx;
		font-size: 32rpx;
		width: 100%;

		.label-content {
			position: relative;

			.icon {
				width: 100%;
				height: 80rpx;
				z-index: 1;
			}

			.icon1 {
				height: 36rpx;
				width: 24rpx;
				position: absolute;
				z-index: 2;
				right: 30rpx;
				line-height: 40rpx;
				top: 22rpx;
			}

			.label-tt {
				color: #fff;
				position: absolute;
				z-index: 2;
				left: 20rpx;
				height: 80rpx;
				line-height: 80rpx;
			}

			.label-tt1 {
				color: #fff;
				position: absolute;
				z-index: 2;
				right: 180rpx;
				height: 80rpx;
				line-height: 80rpx;
				letter-spacing: 4rpx;
				font-size: 30rpx;
			}

			.label-tt2 {
				color: #fff;
				position: absolute;
				z-index: 2;
				right: 80rpx;
				height: 80rpx;
				line-height: 80rpx;
				font-size: 30rpx;
			}
		}
	}

	.banner {
		width: 100%;
		height: 148px;
	}

	.banner swiper {
		width: 100%;
		height: 100%;
	}

	.swiper-item {
		width: 100%;
		height: 100%;
	}

	.swiper-item image {
		width: 100%;
		height: 100%;
	}

	.home {
		padding-left: 40rpx;
		padding-right: 40rpx;
		padding-bottom: 70px;
		padding-top: 72 upx;

		.home-header {
			display: flex;
			align-items: center;
			justify-content: space-between;
			/* #ifdef APP-PLUS */
			height: calc(90rpx + var(--status-bar-height));
			/* #endif */
			/* #ifdef H5 */
			height: calc(90px + var(--status-bar-height));
			/* #endif */

			.logo {
				width: 309rpx;
				height: 46rpx;
			}
		}

		.home-search {
			margin: 16rpx 0 24rpx;
			display: flex;
			align-items: center;
			justify-content: space-between;
		}

		.home-banner {
			width: 670rpx;
			height: 296rpx;
		}

		.home-quick {
			position: relative;
			margin-top: 32rpx;
			padding-left: 120rpx;
			height: 72rpx;
			border-radius: 8rpx;
			overflow: hidden;

			.home-quick__icon {
				position: absolute;
				top: 0;
				left: 0;
				z-index: 999;
				padding-right: 20rpx;
				width: 120rpx;
				height: 100%;
				font-size: 32rpx;
				font-weight: 600;
				color: #FFFFFF;
				text-align: center;
				line-height: 72rpx;
				background: url(../../static/img/quick-nav.png) no-repeat center;
				background-size: cover;
			}

			.home-quick__swiper {
				height: 100%;

				.public-item {
					padding: 0 20rpx;
					margin: -1rpx;
					display: flex;
					align-items: center;
					height: 100%;
					background: linear-gradient(90deg, rgba(213, 40, 255, 0.3) 0%, #050523 100%);
					border-top-right-radius: 8rpx;
					border-bottom-right-radius: 8rpx;
					overflow: hidden;
				}
			}
		}
	}
</style>
