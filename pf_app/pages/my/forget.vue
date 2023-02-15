<template>
	<view class="forget container">
		<u-navbar :title="$t('passwordBack')" bgColor="#14142B" leftIconColor="#F585FF" autoBack fixed placeholder />
		<sy-tabs type="custom" size="large" :data="config"></sy-tabs>
		<u-form ref="formRef" class="sy-form" labelPosition="top" :labelStyle="labelStyle" labelWidth="100%" :model="model" :rules="rules">
			<u-form-item :label="$t('form.account')" prop="account">
				<u-input v-model="model.account" :placeholder="$t('form.accountMsg')" placeholderStyle="color:#707098" color="#707098"></u-input>
			</u-form-item>
			<u-form-item :label="$t('form.code')" prop="code">
				<u-input v-model="model.code" :placeholder="$t('form.codeMsg')" class="code-input" placeholderStyle="color:#707098" color="#707098"></u-input>
				<view class="code-warp">
					<u-code ref="uCode" @change="codeChange" seconds="60" changeText="X秒重新获取"></u-code>
					<u-button class="code-btn" @tap="sendCodeHandler" :text="tips" color="#404066" ></u-button>
				</view>
			</u-form-item>
			<u-form-item :label="$t('form.newPassword')" prop="new_password">
				<u-input v-model="model.new_password" :placeholder="$t('form.newPasswordMsg')" placeholderStyle="color:#707098" color="#707098" password></u-input>
			</u-form-item>
			<u-form-item :label="$t('form.againPassword')" prop="new_password_again">
				<u-input v-model="model.new_password_again" :placeholder="$t('form.againPasswordMsg')" placeholderStyle="color:#707098" color="#707098" password></u-input>
			</u-form-item>
		</u-form>
		<u-button customStyle="margin-top: 44rpx" :text="$t('confirm')" color="linear-gradient(to right, #9041FF, #E323FF)" @click="submitHandler"></u-button>

	</view>
</template>

<script>
	import { mapState, mapMutations } from 'vuex';
	import { userBackApi } from '@/api/user.js';
	import { sendCodeApi } from '@/api/index.js';
	export default {
		name: 'forget',
		data() {
			return {
				config: [
					{ name: 'passwordBack' }
				],
				tips: '',
				labelStyle: {
					'margin-bottom': '16rpx',
					'font-size': '26rpx',
					'line-height': '36rpx',
					'color': '#FFFFFF'
				},
				model: {
					account: undefined,
					code: undefined,
					new_password: undefined,
					new_password_again: undefined,
				},
				rules: {
					account: [
						{ type: 'string', required: true, message: '请输入账号', trigger: 'change' }
					],
					code: [
						{ type: 'string', required: true, message: '请输入验证码', trigger: 'change' }
					],
					new_password: [
						{ type: 'string', required: true, message: '请输入密码', trigger: 'change' }
					],
					new_password_again: [
						{
							validator: this.againPasswordVlidator,
							message: '请再次确认密码',
							trigger: 'change' 
						}
					]
				},
			}
		},
		methods: {
			...mapMutations(['updateToken', 'updateUserInfo']),
			againPasswordVlidator(rule, value, callback) {
				let flag = true
				if (!value) {
					flag = false;
				} else {
					if (this.model.new_password !== value) {
						flag = false;
					}
				}
				return flag;
			},
			// 验证码
			codeChange (text) {
				this.tips = text;
			},
			sendCodeHandler () {
				this.$refs.formRef.validateField(['account'], async (err) => {
					if(this.$refs.uCode.canGetCode && err.length== 0){
						let { account } = this.model;
						let { code, msg } = await sendCodeApi(account.indexOf('@') == -1? 'Sms' : 'Email',{ account, event: 2 });
						if(code == 1) {
							this.$refs.uCode.start();
						} 
						msg && uni.$u.toast(msg);
					}
				})
			},
			submitHandler () {
				this.$refs.formRef.validate().then(res => {
					userBackApi(this.model).then(({code, data, msg}) => {
						if (code == 1) {
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
		created() {
			// this.initCountry();
		}
	}
</script>

<style lang="scss" scoped>
	.forget {
		padding-top: 170rpx;
		padding-left: 40rpx;
		padding-right: 40rpx;
		padding-bottom: 60rpx;
		box-sizing: border-box;
	}
</style>
