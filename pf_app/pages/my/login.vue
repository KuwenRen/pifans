<template>
	<view class="login container">
		<u-navbar :title="$t('login')" bgColor="#14142B" leftIconColor="#F585FF" autoBack fixed placeholder />
		<view class="warp">
			<view class="login-header">
				<view class="title">{{$t('login')}}</view>
				<i18nSwitch type="button" />
			</view>
			<view class="login-banner">
				<view class="slogan">
					<view class="slogan-item">
						{{$t('slogan1')}}
					</view>
					<view class="slogan-item">
						{{$t('slogan2')}}
					</view>
				</view>
			</view>
			<u-form ref="formRef" class="sy-form" labelPosition="top" :labelStyle="labelStyle" labelWidth="100%" :model="model" :rules="rules">
				<u-form-item :label="$t('form.account')" prop="account">
					<u-input v-model="model.account" :placeholder="$t('form.accountMsg')" placeholderStyle="color:#707098" color="#707098"></u-input>
				</u-form-item>
				<u-form-item :label="$t('form.password')" prop="password">
					<u-input v-model="model.password" :placeholder="$t('form.passwordMsg')" placeholderStyle="color:#707098" color="#707098" password></u-input>
				</u-form-item>
			</u-form>
			<u-button customStyle="margin-top: 12rpx" :text="$t('login')" color="linear-gradient(to right, #9041FF, #E323FF)" @click="submitHandler"></u-button>
			<u-button customStyle="margin-top: 32rpx" :text="$t('noAccount')" color="#404066" @click="toRegister"></u-button>

			<view @click="toForget" style="font-size: 26rpx;text-align: center;color: rgba(255,255,255,0.6);margin-top: 30rpx;padding-bottom: 60rpx;">
				{{$t('forget')}}
			</view>			
		</view>
	</view>
</template>

<script>
	import { mapState, mapMutations } from 'vuex';
	import { userLoginApi } from '@/api/user.js';
	export default {
		name: 'login',
		data() {
			return {
				labelStyle: {
					'margin-bottom': '16rpx',
					'font-size': '26rpx',
					'line-height': '36rpx',
					'color': '#FFFFFF'
				},
				model: {
					account: undefined,
					password: undefined,
				},
				rules: {
					account: [
						{ type: 'string', required: true, message: this.$t('form.accountMsg'), trigger: 'change' }
					],
					password: [
						{ type: 'string', required: true, message: this.$t('form.passwordMsg'), trigger: 'change' }
					]
				},
			}
		},
		methods: {
			...mapMutations(['updateToken', 'updateUserInfo']),
			submitHandler () {
				this.$refs.formRef.validate().then(res => {
					userLoginApi(this.model).then(({code, data:{ userinfo }, msg}) => {
						if (code == 1) {
							this.updateToken(userinfo.token);
							this.updateUserInfo(userinfo);
							uni.reLaunch({url: '/pages/index/index?tab=my'});
						} else {
							msg && uni.$u.toast(msg);
						}
					})
				}).catch(err => {
					console.log(err)
				})
			},
			toRegister () {
				uni.navigateTo({url: '/pages/my/register'});
			},
			toForget() {
				uni.navigateTo({url: '/pages/my/forget'});
			},
			initRules () {
				this.rules = {
					account: [
						{ type: 'string', required: true, message:  this.$t('form.accountMsg'), trigger: 'change' }
					],
					password: [
						{ type: 'string', required: true, message: this.$t('form.passwordMsg'), trigger: 'change' }
					]
				}
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
		watch: {
			locale() {
				this.initRules();
			}
		},
		created() {
			// this.initCountry();
		}
	}
</script>

<style lang="scss" scoped>
	.login {
		.warp {
			.login-header {
				margin-top: 32rpx;
				display: flex;
				justify-content: space-between;
				> .title {
					font-size: 48rpx;
					line-height: 76rpx;
					color: #F585FF;
				}
			}
			.login-banner {
				position: relative;
				margin: 32rpx auto;
				width: 670rpx;
				height: 546rpx;
				background: url('/static/img/login-banner.png') no-repeat center;
				background-size: cover;
				.slogan {
					position: absolute;
					top: 94rpx;
					left: 40rpx;
					color: #F585FF;
					font-size: 28rpx;
					font-weight: 600;
					line-height: 40rpx;
				}
			}
			.sy-form {
				.u-border  {
					border-color: transparent!important;
				}
				.code-input {
					border-top-right-radius: 0!important;
					border-bottom-right-radius: 0!important;
				}
				.code-btn {
					height: 44px;
					color: #BEBEDC !important;
					border-top-left-radius: 0;
					border-bottom-left-radius: 0;
				}
			}			
		}

	}
</style>
