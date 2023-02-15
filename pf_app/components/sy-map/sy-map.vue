<template>
	<view class="sy-map">
		<!-- <view class="count">
			<u-icon name="eye-fill" color="#F585FF"size="38rpx"></u-icon>
			<view class="text">
				<view class="text" style="margin-bottom: 10rpx;" v-if="!isShop">{{$t('onlineCount')}}:{{count}}</view>
				<view class="text" v-else>{{$t('商家数量')}}:{{count2}}</view>
			</view>
			<view class="sx" v-if="type == 'complex' && isShop" @click="showPop">
				<text>{{$t('筛选')}}</text>
				<image class="sx-icon" mode="widthFix" src="../../static/img/shaixuan.png"	></image>
			</view>
		</view> -->
		<view v-if="type == 'complex'" class="operate">
			<!-- <u-image width="308rpx" height="88rpx" src="/static/img/label.png" @click="toLabel"></u-image> -->
			<view class="operate-list" v-if="!isShop">
				<image class="map-icon" @click="changeShop" src="@/static/home/v2/mapIcon4.png"></image>
				<text :class="localeIndex==1?'text':'text1'">{{$t('切换模式')}}</text>
			</view>
			<view class="operate-list" v-else>
				<image class="map-icon" @click="changeShop" src="@/static/home/v2/mapIcon4.png"></image>
				<text :class="localeIndex==1?'text':'text1'">{{$t('切换模式')}}</text>
			</view>
			<view class="operate-list">
				<image class="map-icon" @click="toMsg" src="@/static/home/v2/mapIcon2.png"></image>
				<text :class="localeIndex==1?'text':'text1'">{{$t('聊天室')}}</text>
			</view>
			<view class="operate-list">
				<image class="map-icon" @click="toLabel" src="@/static/home/v2/mapIcon1.png"></image>
				<text :class="localeIndex==1?'text':'text1'">{{$t('找上级')}}</text>
			</view>
			<view class="operate-list" v-if="isShop">
				<image class="map-icon" @click="toEdit" src="@/static/img/addShop.png"></image>
				<text :class="localeIndex==1?'text':'text1'">{{$t('商家入驻')}}</text>
			</view>
		</view>

		<view :id="el" :style="`width: 100%;height: ${height};`"></view>
		<u-popup :show="isShowPop" @close="close">
			<view class="pop-wapper">
				<view class="pop-title-wapper">
					<text>{{$t('全部筛选')}}</text>
				</view>
				<view class="pop-sub-title">
					{{$t('国家')}}
				</view>
				<view class="btn-wapper">
					<view @click="tabItem(index,'countryIndex')"
						:class="['btn-item',index==countryIndex?'btn-item-ac':'']" v-for="(item,index) in countryList"
						:key="item.id">
						{{$t(item.name)}}
					</view>
				</view>
				<view class="pop-sub-title">
					{{$t('商家支付类型')}}
				</view>
				<view class="btn-wapper">
					<view @click="tabItem(index,'merchantIndex')"
						:class="['btn-item',index==merchantIndex?'btn-item-ac':'']" v-for="(item,index) in merchantType"
						:key="item.id">
						{{$t(item.name)}}
					</view>
				</view>
				<view class="pop-btn-wapper">
					<view @click="reset">{{$t('重置')}}</view>
					<view @click="submit">{{$t('查看筛选内容')}}</view>
				</view>
			</view>
		</u-popup>
	</view>
</template>

<script>
	import {
		mapState
	} from 'vuex';
	import AMapLoader from '@amap/amap-jsapi-loader';
	import {
		getLocationUserApi
	} from '../../api/user.js';
	import {
		getMerchantType,
		getMerchantList,
		getCountryListApi
	} from '@/api/index.js';
	export default {
		name: "sy-map",
		props: {
			el: String,
			type: {
				type: String,
				default: 'complex',
			},
			height: {
				type: String,
				default: '100vh'
			}
		},
		data() {
			return {
				isShop: false,
				isShowPop: false,
				count: 4557,
				count2: 3,
				AMap: null,
				AMapInstance: null,
				map: null,
				merchantType: [],
				merchantIndex: null,
				countryList: [],
				countryIndex: null,
				timeId: 0,
				location: {},
			};
		},
		computed: {
			...mapState({
				locale: state => state.locale,
				token: state => state.token,
				userInfo: state => state.userInfo,
			}),
			localeIndex() {
				return this.locale == 'zh-cn' ? 1 : 2;
			},
			mapLang() {
				let lang = this.locale == 'zh-cn' ? 'zh_cn' : 'en';
				return lang;
			}
		},
		methods: {
			toMsg() {
				if (!this.token) {
					uni.navigateTo({
						url: "/pages/my/login"
					})
				} else {
					uni.navigateTo({
						url: "/pages/msgList/msgList"
						// url: "/pages/chat-room/chat-room"
					})
				}

			},
			tabItem(index, name) {
				this[name] = index
			},
			showPop() {
				this.getCountryListApi()
				this.getMerchantType()
				this.isShowPop = true
			},
			close() {
				this.isShowPop = false
			},
			changeShop() {
				this.isShop = !this.isShop
				this.initMap(!this.isShop)
				setTimeout(() => {
					if (!this.isShop) {
						this.initUserMaker(this.location)
					} else {
						this.getMerchantList(this.location)
					}
				}, 100)
			},
			reset() {
				this.merchantIndex = null
				this.countryIndex = null
				this.isShowPop = false
				this.initMap()
				this.getMerchantList()
			},
			submit() {
				this.isShowPop = false
				this.initMap()
				this.getMerchantList()
			},
			// 获取商家数据
			async getMerchantList(data, loading) {
				let {
					lat,
					lng
				} = data
				if (!loading) {
					uni.showLoading({
						// mask: true
					})
				}

				let country_id = ""
				let merchant_type_id = ""

				if (this.merchantIndex != null) {
					merchant_type_id = this.merchantType[this.merchantIndex].id
				}
				if (this.countryIndex != null) {
					country_id = this.countryList[this.countryIndex].id
				}


				let res = await getMerchantList({
					country_id,
					merchant_type_id,
					lat,
					lng
				})
				if (res.code == 1) {
					this.count2 = res.data.count;
					this.render(res)
				}
			},
			render(res) {
				try {
					res.data.list.forEach(item => {
						item.type = "shop"
						item.image = require(`@/static/img/shopIcon${item.status}.png`)
						this.createMarker({
							longitude: item.lng,
							latitude: item.lat,
							icon: item.image,
							data: item
						})
					})
					uni.hideLoading()
				} catch (e) {
					setTimeout(() => {
						this.render(res)
					}, 100)
				}
			},

			//国家列表
			async getCountryListApi() {
				if (this.countryList.length) return
				let res = await getCountryListApi()
				if (res.code == 1) {
					this.countryList = res.data
				}
			},

			//支付类型列表
			async getMerchantType() {
				if (this.merchantType.length) return
				let res = await getMerchantType()
				if (res.code == 1) {
					this.merchantType = res.data
				}
			},

			async initMap(isMy) {
				let res = await this.getCurPosition()
				// var map = new BMapGL.Map(this.el,{minZoom:13,maxZoom:20});
				var map = new BMapGL.Map(this.el);
				this.map = map;
				console.log(13456)
				// 创建地图实例 
				this.location = {
					lat: res.latitude,
					lng: res.longitude
				}
				var point = new BMapGL.Point(res.longitude, res.latitude);
				// 创建点坐标 
				map.centerAndZoom(point, 15);
				map.enableScrollWheelZoom(true); //开启鼠标滚轮缩放
				// 初始化地图，设置中心点坐标和地图级别 
				var scaleCtrl = new BMapGL.ScaleControl(); // 添加比例尺控件
				map.addControl(scaleCtrl);
				var zoomCtrl = new BMapGL.ZoomControl(); // 添加缩放控件
				map.addControl(zoomCtrl);


				map.addEventListener('click', (e) => {
					this.$emit("mapClick")
					console.log(e)

				});
				map.addEventListener("dragend", () => {
					if (!this.isShop) {
						this.initUserMaker(map.getCenter(), true)
					} else {
						this.getMerchantList(map.getCenter(), true)
					}
				});
				// map.addEventListener("zoomend", function () {
				// 	var cp = map.getBounds(); // 返回map可视区域，以地理坐标表示 
				// 	var sw = cp.getSouthWest(); // 返回矩形区域的西南角  
				// 	var ne = cp.getNorthEast(); // 返回矩形区域的东北角 
				// });
				let extData = null;
				if (isMy) {
					if (this.token) {
						let {
							id,
							nickname,
							avatar_code,
							wx_number,
							declaration,
							province,
							city
						} = this.userInfo;
						extData = {
							id,
							nickname,
							avatar_code,
							wx_number,
							declaration,
							adress: `${province}-${city}`
						}
					}
					this.createMarker({
						longitude: res.longitude,
						latitude: res.latitude,
						icon: require("@/static/img/user/new_mark1.png"),
						data: extData
					})
				}

			},
			// 获取当前位置
			getCurPosition() {
				return new Promise(reslove => {
					uni.getLocation({
						type:'gcj02',
						success(res) {
							reslove(res)
						}
					})
				})

			},


			// 创建图标
			createMarker({
				longitude,
				latitude,
				icon,
				data
			}) {
				var point = new BMapGL.Point(parseFloat(longitude), parseFloat(latitude));
				// 创建贴图纹理Icon
				var icon = new BMapGL.Icon(icon, new BMapGL.Size(30, 30));
				// 创建带高度的点，这里只需要size和icon
				var marker = new BMapGL.Marker3D(point, 0, {
					size: 30,
					icon: icon
				});
				// 将点添加到地图上
				this.map.addOverlay(marker);
				marker.data = data
				marker.addEventListener('click', e => {
					setTimeout(() => {
						this.$emit('makerClick', e.target.data);
					}, 100)
				})
			},


			toEdit() {
				uni.navigateTo({
					// url: "/pages/editShopAdd/editShopAdd"
					url: "/pages/shopjoin/shopjoin"
				})
			},




			toLabel() {
				uni.navigateTo({
					url: "/pages/labelComp/labelComp"
				})
				// this.$emit('changeIndex', 'label');
			},
			async initUserMaker(data2, loding) {

				let {
					lat,
					lng
				} = data2;
				if (!loding) {
					uni.showLoading({
						// mask: true
					})
				}
				let {
					code,
					data,
					msg
				} = await getLocationUserApi({
					lat,
					lng
				})
				this.count = data.count;
				let map = (data) => {
					data.forEach(v => {
						let {
							id,
							nickname,
							avatar_code,
							wx_number,
							declaration = '暂无宣言',
							province,
							city,
							log,
							lat
						} = v;
						if (log && lat) {
							this.createMarker({
								longitude: log,
								latitude: lat,
								icon: require(`@/static/img/user/new_mark${avatar_code}.png`),
								data: {
									id,
									nickname,
									avatar_code,
									wx_number,
									declaration,
									adress: `${province}-${city}`
								},
							})
						}
					})
				}
				map(data)
				uni.hideLoading()

			},
		},
		watch: {
			isShop() {
				clearInterval(this.timeID)
			},
			locale(cur, old) {
				let lang = this.locale == 'zh-cn' ? 'zh_cn' : 'en';
				this.AMapInstance.setLang(lang);
			}
		},
		async mounted() {
			// this.init();
			if (location.href.includes("type")) {
				this.isShop = true
			}
			await this.initMap(!this.isShop)
			if (!this.isShop) {
				this.initUserMaker(this.location, true)
			} else {
				this.getMerchantList(this.location, true)
			}
		}
	}
</script>

<style lang="scss" scoped>
	.pop-wapper {
		background-color: #232344;
		color: #CF21FE;
		padding: 0 20rpx;
	}

	.pop-title-wapper {
		text-align: center;
		padding: 20rpx 0;
	}

	.pop-sub-title {
		font-size: 28rpx;
		color: #FFF;
		margin: 20rpx 0;
	}

	.btn-wapper {
		display: flex;
		flex-wrap: wrap;
		// justify-content: space-between;
		width: calc(100% + 30rpx);
		max-height: 400rpx;
		overflow: hidden;
		overflow-y: scroll;
	}

	.btn-item {
		width: 226rpx;
		border: 1px solid #323256;
		background-color: #323256;
		color: #EEB6B1;
		border-radius: 10rpx;
		height: 64rpx;
		text-align: center;
		line-height: 64rpx;
		margin-bottom: 30rpx;
		margin-right: 10rpx;
		font-size: 28rpx;
		white-space: nowrap;
		overflow: hidden;
		text-overflow: ellipsis;
	}

	.btn-item-ac {
		width: 226rpx;
		border: 1px solid #cf21fe;
		color: #cf21fe;
		border-radius: 10rpx;
		height: 64rpx;
		text-align: center;
		line-height: 64rpx;
		margin-bottom: 30rpx;
		margin-right: 10rpx;
		font-size: 28rpx;
		background-color: none;
	}

	.pop-btn-wapper {
		background: #323256;
		display: flex;
		margin-bottom: 40rpx;
		height: 100rpx;
		border-radius: 10rpx;
		overflow: hidden;
	}

	.pop-btn-wapper view {
		height: 100%;
		display: flex;
		align-items: center;
		justify-content: center;
		flex: 1;
		font-size: 28rpx;
	}

	.pop-btn-wapper view:last-child {
		background: linear-gradient(266deg, #e323ff 2%, #9041ff 92%);
		color: #FFF;
	}


	.sx {
		color: #F585FF;
		font-size: 24rpx;
		display: flex;
		align-items: center;
	}

	.sx-icon {
		width: 40rpx;
		margin-left: 10rpx;
	}



	.sy-map {
		position: relative;

		.count {
			position: absolute;
			top: 44rpx;
			left: 40rpx;
			display: flex;
			align-items: center;
			z-index: 99;
			color: #FFFFFF;
			text-shadow: 0px 0px 3px rgba(0, 0, 0, 0.86);
			width: 680rpx;
			display: flex;

			.text {
				margin-left: 16rpx;
				font-size: 32rpx;
				flex: 1;
				color: #F585FF;
			}
		}

		.operate {
			position: absolute;
			top: 180rpx;
			left: 40rpx;
			display: flex;
			flex-direction: column;
			z-index: 99;

			.operate-list {
				color: #CF21FE;
				display: flex;
				display: flex;
				flex-direction: column;
				font-size: 26rpx;
				margin-bottom: 20rpx;

				.map-icon {
					width: 80rpx;
					height: 80rpx;
				}

				.text {
					height: 60rpx;
					text-align: center;
					line-height: 60rpx;
					margin-left: -7rpx;
				}

				.text1 {
					height: 60rpx;
					text-align: center;
					line-height: 60rpx;
					margin-left: -30rpx;
				}

			}
		}
	}
</style>
