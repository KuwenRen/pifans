<!-- 
@author x 
@title 商家的搜索
 -->
<template>
	<view class="shop-search">
		<u-header :title="$t('search')"></u-header>
		<view class="box">
			<view style="padding: 0 30rpx;">
				<u-search color="#ffffff" bgColor="#323256" searchIconColor="#C4C4C4" borderColor="#404065"
					placeholderColor="rgba(255,255,255,0.8)" :placeholder="$t('searchPlace')" height="80" shape="square"
					:showAction="keyword!=''" v-model="keyword" border-color @custom="search"></u-search>
			</view>
			<view class="list">
				<view v-for="(item,key) in categorylist" class="item" :class="typeId==item.key?'active':''"
					@click="typeId=item.key">
					{{$t(item.name)}}
				</view>
			</view>
			<view v-if="typeId!=1" class="category">
				<view v-if="typeId==2" class="type" @click="showType=!showType">
					<text>{{typeName}}</text>
					<view v-if="showType" class="popup">
						<category-popup :datalist="business_type" v-model="shopId"></category-popup>
					</view>
				</view>
				<view v-if="typeId==3" class="city" @click="goCity">
					<text>{{cityname}}</text>
				</view>
			</view>
		</view>
		<u-list @scrolltolower="scrolltolower" v-if="!haveMore">
			<view style="padding: 0 30rpx;">
				<text v-for="(item,key) in list">
					<shop-item :item="item"></shop-item>
				</text>
			</view>
		</u-list>
		<view class="tips" v-if="haveMore">
			<text>暂无数据或数据已加载完毕~</text>
		</view>
	</view>
</template>

<script>
	import {
		typelist,
		getMechantList
	} from '@/api/shop'
	export default {
		data() {
			return {
				haveMore: false,
				business_type: [],
				shopId: 0, //类别id
				showType: false,
				typeName: '请选择类别',
				categorylist: [{
						key: 1,
						name: 'all'
					},
					{
						key: 2,
						name: 'category'
					},
					{
						key: 3,
						name: 'city'
					}
				], //分类列表
				keyword: '', //关键词
				typeId: 1, //分类id
				limit: 10,
				page: 1,
				list: [], //列表
				province: null
			}
		},
		computed: {
			cityname() {
				let name = '请选择城市';
				let o = this.$store.state.city;
				if (o.name) {
					this.province = o.name;
					name = o.name;
				}
				return name
			},
			localeIndex() {
				return this.locale == 'zh-cn' ? 1 : 2;
			},
		},
		watch: {
			shopId(id) {
				if (id) {
					this.typeName = '请选择类别';
					for (let item of this.business_type) {
						if (item.id == id) {
							this.typeName = item.typename;
							break;
						}
					}
					this.province = '';
					this.getMechantList();
				}
			},
			typeId(val) {
				this.page = 1;
				if (val == 1) {
					this.shopId = null;
					this.province = '';
				}
				this.getMechantList();
			},
			province(val) {
				if (val) {
					this.shopId = null
					this.getMechantList();
				}
			}
		},
		onLoad() {
			this.typelist();
			this.getMechantList();
		},
		methods: {
			/* 获取商家类型 */
			typelist() {
				typelist().then(res => {
					this.business_type = res.data;
				})
			},
			/* 滚动到底部 */
			scrolltolower() {
				this.page++;
				this.getMechantList();
			},
			/* 搜索 */
			search() {
				this.page = 1;
				this.getMechantList();
			},
			/* 商家列表 */
			getMechantList() {
				getMechantList({
					business_type_id: this.shopId,
					page: this.page,
					limit: this.limit,
					keyword: this.keyword,
					province: this.province
				}).then(res => {
					if (this.page == 1) {
						this.list = [];
					}
					if (res.data.list.length <= 0) {
						if (this.page == 1) {
							this.haveMore = true
						}
						uni.showToast({
							title: '暂无数据~',
							icon: 'none'
						})
						return
					}
					this.haveMore = false
					if (this.page == 1) {
						this.list = res.data.list;
					} else {
						this.list = this.list.concat(res.data.list);
					}
				})
			},
			/* 城市选取 */
			goCity() {
				uni.navigateTo({
					url: '/pages/city-choose/city-choose'
				})
			}
		}
	}
</script>

<style lang="scss" scoped>
	.box {
		position: sticky;
		z-index: 99;
		top: calc(90rpx + var(--status-bar-height));
		background-color: #050523;
	}

	.list {
		display: flex;
		justify-content: space-around;
		border-bottom: 1px solid rgba(#fff, 0.3);

		.item {
			position: relative;
			color: #fff;
			height: 90rpx;
			display: flex;
			justify-content: center;
			align-items: center;

			&.active {
				color: #8D3FFA;
			}
		}

		.active:before {
			position: absolute;
			bottom: 0;
			content: '';
			width: 120rpx;
			height: 2px;
			background: linear-gradient(266deg, #e323ff 2%, #9041ff 92%);
		}
	}

	.popup {
		position: absolute;
		left: 0;
		top: 64rpx;
		width: 100%;
		height: 100vh;
		background-color: rgba(#000, 0.5);
	}

	.category {
		position: relative;
		padding: 16rpx 30rpx;
		display: flex;
		align-items: center;
		background-color: #14142B;
		color: #fff;
		font-size: 24rpx;
	}

	.tips {
		width: 100%;
		height: 80rpx;
		color: #fff;
		text-align: center;
	}
</style>
