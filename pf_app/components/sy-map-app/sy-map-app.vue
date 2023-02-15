<template>
	<view class="sy-map">
		<map
			:style="`width: 100%;height: ${height || fullHeight}`"
			:id="el" :markers="makers" scale="15"
			layer-style="fef82cea8eb7b47b16f5571321f1592f"
			@tap="" @markertap="">
				<cover-view class="count">{{$t('onlineCount')}}:{{count}}</cover-view>
				<cover-image v-if="type == 'complex'" class="label" style="top: 120rpx;" src="/static/img/label.png" @click="toLabel"/>
				<!-- <cover-image v-if="type == 'complex'" class="chart" style="top: 220rpx;" src="/static/img/chart.png"/> -->
		</map>
	</view>
</template>

<script>
	import { mapState } from 'vuex';
	import { getLocationUserApi } from '../../api/user.js';
	export default {
		name:"sy-map",
		props: {
			el: String,
			type: {
				type: String,
				default: 'complex',
			},
			height: String
		},
		data() {
			return {
				count: 0,
				countMaker: '',
				AMap: null,
				AMapInstance: null,
				fullHeight: '100vh',
				makers: []
			};
		},
		computed: {
			...mapState({
				locale: state => state.locale,
				token: state => state.token,
				userInfo: state => state.userInfo,
			}),
			mapLang() {
				let lang = this.locale == 'zh-cn'? 'zh_cn': 'en';
				return lang;
			}
		},
		methods: {
			toLabel() {
				this.$emit('changeIndex','label')
			},
			init () {
				const { windowHeight, windowWidth } = uni.getSystemInfoSync();
				const self = this;
				self.AMapInstance = uni.createMapContext(self.el, self);
				this.fullHeight = (windowHeight - 70) + 'px';
				this.initMaker();
				this.initCenter()
			},
			initMaker() {
				const self = this;
				getLocationUserApi().then(({ code, data, msg }) => {
					self.makers = data.map((v,i) => {
						let { id, nickname, avatar_code, wx_number,  declaration = '--', province, city, log, lat } = v;
						if(log && lat)  {
							return {
								id,
								latitude: lat,
								longitude: log,
								iconPath: `/static/img/user/mark${avatar_code}.png`,
								width: 37,
								heigth: 37,
								extData: v
							};
						} else {
							return null;
						}
					})
					self.count = self.count + self.makers.length;
				})
			},
			initCenter() {
				
			}
		},
		watch: {
			locale(cur, old) {
				let lang = this.locale == 'zh-cn'? 'zh_cn': 'en';
			}
		},
		mounted() {
			this.init();
		}
	}
</script>

<style lang="scss" scoped>
	.sy-map {
		position: relative;
		.label, .chart {
			position: absolute;
			left: 40rpx;
			width: 80rpx;
			height: 80rpx;
		}
		.count {
			position: absolute;
			top: 64rpx;
			left: 40rpx;
			display: flex;
			align-items: center;
			z-index: 99;
			color: #F585FF;
			text-shadow: 0px 0px 3px rgba(0, 0, 0, 0.86);
			z-index: 9999;
			.text {
				margin-left: 16rpx;
				font-size: 32rpx;
			}
		}
	}
</style>
