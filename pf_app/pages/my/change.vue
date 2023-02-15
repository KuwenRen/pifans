<template>
	<view class="change container">
		<u-navbar :title="$t('passwordChange')" bgColor="#14142B" leftIconColor="#F585FF" autoBack fixed placeholder />
		<view class="warp">
			<sy-tabs type="custom" size="large" :data="config"></sy-tabs>
			<u-form ref="formRef" class="sy-form" labelPosition="top" :labelStyle="labelStyle" labelWidth="100%" :model="model" :rules="rules">
				<u-form-item :label="$t('form.oldPassword')" prop="old_password" password>
					<u-input v-model="model.old_password" :placeholder="$t('form.oldPasswordMsg')" placeholderStyle="color:#707098" color="#707098"></u-input>
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
	</view>
</template>

<script>
	import { mapState, mapMutations } from 'vuex';
	import { userChangeApi } from '@/api/user.js';
	import { sendCodeApi } from '@/api/index.js';
	export default {
		name: 'change',
		data() {
			return {
				config: [
					{ name: 'passwordChange' }
				],
				labelStyle: {
					'margin-bottom': '16rpx',
					'font-size': '26rpx',
					'line-height': '36rpx',
					'color': '#FFFFFF'
				},
				model: {
					old_password: undefined,
					new_password: undefined,
					new_password_again: undefined,
				},
				rules: {
					old_password: [
						{ type: 'string', required: true, message: this.$t('form.oldPassword'), trigger: 'change' }
					],
					new_password: [
						{ type: 'string', required: true, message: this.$t('form.newPassword'), trigger: 'change' }
					],
					new_password_again: [
						{ validator: this.againPasswordVlidator, message: this.$t('form.againPassword'), trigger: 'change' }
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
			submitHandler () {
				this.$refs.formRef.validate().then(res => {
					userChangeApi(this.model).then(({code, data, msg}) => {
						msg && uni.$u.toast(msg);
						if (code == 1) {
							uni.reLaunch({url: '/pages/index/index?tab=my'});
						}
					})
				}).catch(err => {
					console.log(err)
				})
			},
			toRegister () {
				uni.navigateTo({url: '/pages/my/register'});
			},
			initRules() {
				this.rules = {
					old_password: [
						{ type: 'string', required: true, message: this.$t('form.oldPassword'), trigger: 'change' }
					],
					new_password: [
						{ type: 'string', required: true, message: this.$t('form.newPassword'), trigger: 'change' }
					],
					new_password_again: [
						{ validator: this.againPasswordVlidator, message: this.$t('form.againPassword'), trigger: 'change' }
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
		created() {
			// this.initRules()
			// this.initCountry();
		},
		mounted() {
			
		}
	}
</script>

<style lang="scss" scoped>
	.change {
		.warp {
			padding-top: 170rpx;
			padding-bottom: 60rpx;
		}
	}
</style>
