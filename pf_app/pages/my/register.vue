<template>
	<view class="register container">
		<u-navbar :title="$t('register')" bgColor="#14142B" leftIconColor="#F585FF" autoBack fixed placeholder />
		<view class="warp">
			<view class="register-header">
				<view class="title">{{$t('register')}}</view>
				<i18nSwitch type="button" />
			</view>
			<view class="register-banner">
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
				<u-form-item :label="$t('form.region')" prop="country" @click="countryShow = true" >
					<u-input v-model="countryItem['name'+localeIndex]" :placeholder="$t('form.regionMsg')" readonly placeholderStyle="color:#707098" color="#707098" suffixIcon="arrow-down-fill"></u-input>
				</u-form-item>
				<u-form-item v-if="model.type == 1" :label="$t('form.phone')" prop="account">
					<u-input v-model="model.account" :placeholder="$t('form.accountMsg')" placeholderStyle="color:#707098" color="#707098" ></u-input>
				</u-form-item>
				<u-form-item v-else :label="$t('form.email')" prop="account">
					<u-input v-model="model.account" :placeholder="$t('form.accountMsg')" placeholderStyle="color:#707098" color="#707098"></u-input>
				</u-form-item>
				<u-form-item :label="$t('form.code')" prop="code">
					<u-input v-model="model.code" :placeholder="$t('form.codeMsg')" class="code-input" placeholderStyle="color:#707098" color="#707098"></u-input>
					<view class="code-warp">
						<u-code ref="uCode" @change="codeChange" seconds="60" :startText="$t('form.codeStartText')" :changeText="$t('form.codeChangeText')" :endText="$t('form.codeStartText')"></u-code>
						<u-button class="code-btn" @tap="sendCodeHandler" :text="tips" color="#404066" ></u-button>
					</view>
				</u-form-item>
				<u-form-item :label="$t('form.invitation')" prop="invite_code">
					<u-input v-model="model.invite_code" :placeholder="$t('form.invitationMsg')" placeholderStyle="color:#707098" color="#707098"></u-input>
				</u-form-item>
				<u-form-item :label="$t('form.password')" prop="password">
					<u-input v-model="model.password" :placeholder="$t('form.passwordMsg')" placeholderStyle="color:#707098" color="#707098" password></u-input>
				</u-form-item>
			</u-form>

			<u-button customStyle="margin-top: 40rpx" :text="$t('register')" color="linear-gradient(to right, #9041FF, #E323FF)" @click="submitHandler"></u-button>
			<u-button customStyle="margin-top: 48rpx" :text="$t('hadAccount')" color="#404066" @click="toLogin"></u-button>

			<view style="font-size: 26rpx;text-align: center;color: #F96E6E;margin-top: 30rpx;padding-bottom: 60rpx;">
				{{$t('form.ban')}}
			</view>		
		</view>
		<u-picker :show="countryShow" :columns="[countryList]" :keyName="'name'+localeIndex" @confirm="confirmCountry" @cancel="countryShow = false"></u-picker>
	</view>
</template>

<script>
	import { mapState } from 'vuex';
	import { userRegisterApi } from '@/api/user.js';
	import { sendCodeApi, getCountryListApi } from '@/api/index.js';
	export default {
		name: 'register',
		data() {
			return {
				labelStyle: {
					'margin-bottom': '16rpx',
					'font-size': '26rpx',
					'line-height': '36rpx',
					'color': '#FFFFFF'
				},
				tips: '',
				model: {
					type: 1,
					country: undefined,
					account: undefined,
					password: undefined,
					code: undefined,
					invite_code: undefined
				},
				rules: {
					country: [
						{ type: 'string', required: true, message: this.$t('form.regionMsg'), trigger: 'change' }
					],
					account: [
						{ type: 'string', required: true, message: this.$t('form.accountMsg'), trigger: 'change' }
					],
					password: [
						{ type: 'string', required: true, message: this.$t('form.passwordMsg'), trigger: 'change' }
					],
					code: [
						{ type: 'string', required: true, message: this.$t('form.codeMsg'), trigger: 'change' }
					]
				},
				countryShow: false,
				countryItem: {},
				countryList: [1,23,4]
			}
		},
		methods: {
			initCountry() {
				getCountryListApi().then(({code, data, msg}) => {
					this.countryList = code == 1? data : [];
				})
			},
			confirmCountry({indexs, value}) {
				this.model.country = value[0].name;
				this.model.type = indexs[0] + 1;
				this.countryItem = value[0];
				this.countryShow = false;
			},
			// 验证码
			codeChange (text) {
				this.tips = text;
			},
			sendCodeHandler () {
				this.$refs.formRef.validateField(['country', 'account'], async (err) => {
					if(this.$refs.uCode.canGetCode && err.length== 0){
						let { type, account } = this.model;
						let { code, msg } = await sendCodeApi(type == 1? 'Sms' : 'Email',{ account, event: 1 });
						if(code == 1) {
							this.$refs.uCode.start();
						} else {
							msg && uni.$u.toast(msg);
						}
					}
				})
			},
			submitHandler () {
				this.$refs.formRef.validate().then(res => {
					userRegisterApi(this.model).then(({code, data, msg}) => {
						if (code == 1) {
							uni.navigateTo({url: '/pages/my/login'});
						}
						msg && uni.$u.toast(msg);
					})
				}).catch(err => {
					console.log(err)
				})
			},
			toLogin () {
				uni.navigateTo({url: '/pages/my/login'});
			},
			initRules() {
				this.rules = {
					country: [
						{ type: 'number', required: true, message: this.$t('form.regionMsg'), trigger: 'change' }
					],
					account: [
						{ type: 'string', required: true, message: this.$t('form.accountMsg'), trigger: 'change' }
					],
					password: [
						{ type: 'string', required: true, message: this.$t('form.passwordMsg'), trigger: 'change' }
					],
					code: [
						{ type: 'string', required: true, message: this.$t('form.codeMsg'), trigger: 'change' }
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
			this.initCountry();
		},
		onLoad({ invite_code }) {
			if(!invite_code) this.model.invite_code = invite_code;
		}
	}
</script>

<style lang="scss" scoped>
	.register {
		.warp {
			
		}
		.register-header {
			margin-top: 32rpx;
			display: flex;
			justify-content: space-between;
			> .title {
				font-size: 48rpx;
				line-height: 76rpx;
				color: #F585FF;
			}
		}
		.register-banner {
			position: relative;
			margin: 32rpx auto;
			width: 670rpx;
			height: 546rpx;
			background: url('/static/img/register-banner.png') no-repeat center;
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
</style>
