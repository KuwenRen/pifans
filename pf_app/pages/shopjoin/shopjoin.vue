<!-- 
@author x 
@title 商家入住
 -->
<template>
	<view class="shopjoin">
		<u-header title="商家入驻"></u-header>
		<view class="form">
			<view class="item">
				<text>商家名称</text>
				<input class="input" v-model="formData.name" placeholder="请输入商家名称" />
			</view>
			<view class="item">
				<text style="margin-bottom: 16px;">商家图片</text>
				<upload-files @deleteClick="deleteClick" @uploadImage="uploadImage"></upload-files>
			</view>
			<view class="item">
				<text>商家联系方式</text>
				<input class="input" v-model="formData.phone" type="number" maxlength="11" placeholder="请输入商家联系方式" />
			</view>
			<view class="item" @click="showBusiness_type=!showBusiness_type">
				<text>商家类别</text>
				<view class="i">
					<input class="input" v-model="business_type_name" disabled placeholder="请选择商家类别" />
					<image v-if="showBusiness_type" class="icon" src="../../static/img/up.png" mode="widthFix"></image>
					<image v-else class="icon" src="../../static/img/down.png" mode="widthFix"></image>
					<category-popup v-if="showBusiness_type" :datalist="business_type"
						v-model="formData.business_type_id">
					</category-popup>
				</view>
			</view>
			<view class="item" @click="countryChoose">
				<text>国家</text>
				<view class="i">
					<input class="input" v-model="country_name" disabled placeholder="请选择国家" />
					<image class="icon" src="../../static/img/down.png" mode="widthFix"></image>
				</view>
			</view>
			<view class="item" @click="showMerchant_type=!showMerchant_type">
				<text>支付方式</text>
				<view class="i">
					<input class="input" v-model="merchant_type_name" disabled placeholder="请选择支付类型" />
					<image v-if="showMerchant_type" class="icon" src="../../static/img/up.png" mode="widthFix"></image>
					<image v-else class="icon" src="../../static/img/down.png" mode="widthFix"></image>
					<category-popup v-if="showMerchant_type" :datalist="merchant_type"
						v-model="formData.merchant_type_id">
					</category-popup>
				</view>
			</view>
			<view class="item" @click="cityChoose">
				<text>所在地区</text>
				<view class="i">
					<input class="input" v-model="city_name" disabled placeholder="请选择所在地区" />
					<image class="icon" src="../../static/img/down.png" mode="widthFix"></image>
				</view>
			</view>
			<view class="item">
				<text>详细地址</text>
				<input class="input" v-model="formData.address" placeholder="请输入商家详细地址" />
			</view>
			<view class="item">
				<text>社交账号(选填)</text>
				<view class="i" @click="showSocial_account_type_list=!showSocial_account_type_list">
					<input class="input" v-model="social_account_type_list_name" disabled placeholder="请选择社交账号" />
					<image v-if="showSocial_account_type_list" class="icon" src="../../static/img/up.png"
						mode="widthFix"></image>
					<image v-else class="icon" src="../../static/img/down.png" mode="widthFix"></image>
					<category-popup v-if="showSocial_account_type_list" :datalist="social_account_type_list"
						v-model="formData.social_account_type">
					</category-popup>
				</view>
				<input class="input" v-model="formData.social_account" placeholder="请输入社交账号" />
			</view>
			<view class="item">
				<text>商家简介</text>
				<textarea class="txt" v-model="formData.describe" placeholder="请输入商家简介"></textarea>
			</view>
			<view class="item">
				<text>商品详情</text>
				<jin-edit placeholder="请输入商品详情" @editOk="editOk" uploadFileUrl="https://pifans.app/api/file/uploadAvatar">
				</jin-edit>
			</view>
			<u-button customStyle="margin-top: 32rpx" :text="$t('save')"
				color="linear-gradient(to right, #9041FF, #E323FF)" @click="submit"></u-button>
		</view>
	</view>
</template>

<script>
	import {
		getMerchantType,
		typelist,
		createMechant
	} from '@/api/shop'
	import {
		getSocialAccount
	} from '@/api/user'
	export default {
		data() {
			return {
				formData: {}, //表单
				business_type: [], //商家类别
				showBusiness_type: false,
				merchant_type: [], //商家支付类型
				showMerchant_type: false,
				social_account_type_list: [], //列表
				showSocial_account_type_list: false,
				imagelist: [] //图片列表
			}
		},
		computed: {
			/* 支付类型 */
			merchant_type_name() {
				let name = '';
				for (let item of this.merchant_type) {
					if (item.id == this.formData.merchant_type_id) {
						name = item.typename;
					}
				}
				return name
			},
			/* 国家名称 */
			country_name() {
				let o = this.$store.state.country;
				this.formData.country_id = o.id;
				return o.name
			},
			/* 城市名称 */
			city_name() {
				let o = this.$store.state.city;
				this.formData.region = o.id;
				return o.name
			},
			/* 商家类型 */
			business_type_name() {
				let name = '';
				for (let item of this.business_type) {
					if (item.id == this.formData.business_type_id) {
						name = item.typename;
					}
				}
				return name
			},
			/* 社区类型名称 */
			social_account_type_list_name() {
				let name = '';
				for (let item of this.social_account_type_list) {
					if (item.id == this.formData.social_account_type) {
						name = item.name;
					}
				}
				return name
			}
		},
		onLoad() {
			Promise.all([this.getMerchantType(), this.typelist(), this.getSocialAccount()]).then(() => {
				this.formData = this.resetFromData();
			})
		},
		methods: {
			async getSocialAccount() {
				let res = await getSocialAccount();
				this.social_account_type_list = res.data;
			},
			/* 商家支付类型 */
			async getMerchantType() {
				let res = await getMerchantType();
				this.merchant_type = res.data;
			},
			/* 商家类别 */
			async typelist() {
				let res = await typelist();
				this.business_type = res.data.map(item => {
					item.checked = false;
					return item
				})
			},
			/* 删除 */
			deleteClick(i) {
				this.imagelist.splice(i, 1);
			},
			/* 图片上传 */
			uploadImage(url) {
				this.imagelist.push(url);
			},
			/* 商家类型选取 */
			categoryClick(o) {
				this.business_type = this.business_type.map(item => {
					if (o.id == item.id) {
						item.checked = !item.checked;
					}
					return item
				})
			},
			/* 生成表单 */
			resetFromData() {
				return {
					merchant_type_id: 0, //支付类别
					business_type_id: 0, //商家类别
					country_id: 0, //国家
					region: 0, //所在地区
					phone: '', //联系方式
					describe: '', //简介
					main_goods: '', //商品说明
					address: '', //地址详情
					social_account: '', //社区账户
					social_account_type: 0, //社区类型
					name: '', //商家名称
					image: ''
				}
			},
			editOk(res) {
				this.formData.main_goods = res.html;
			},
			/* 提交 */
			submit() {
				this.formData.image = this.imagelist.join(',');

				createMechant(this.formData).then(res => {
					if (res.code == 0) {
						uni.showToast({
							title: res.msg,
							icon: 'error'
						})
					} else {
						uni.showToast({
							title: res.msg,
							icon: 'success'
						})
						setTimeout(() => {
							uni.navigateBack();
						}, 800)
					}
				})
			},
			/* 国家选择 */
			countryChoose() {
				uni.navigateTo({
					url: '/pages/country-choose/country-choose'
				})
			},
			/* 所在地区 */
			cityChoose() {
				uni.navigateTo({
					url: '/pages/city-choose/city-choose'
				})
			}
		}
	}
</script>

<style lang="scss" scoped>
	.form {
		padding: 32rpx;
	}

	.item {
		margin-bottom: 32rpx;
		display: flex;
		flex-direction: column;
		color: #FFFFFF;

		.i {
			position: relative;
		}

		.icon {
			position: absolute;
			bottom: 26rpx;
			right: 32rpx;
			width: 40rpx;
		}

		.input {
			margin-top: 16rpx;
			padding: 24rpx;
			background-color: #14142B;
			border-radius: 4px;
		}

		.txt {
			margin-top: 16rpx;
			padding: 24rpx;
			box-sizing: border-box;
			width: 100%;
			height: 200rpx;
			background-color: #14142B;
			border-radius: 4px;
		}
	}
</style>
