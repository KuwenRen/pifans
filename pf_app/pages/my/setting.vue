<template>
	<view class="setting container">
		<u-navbar :title="$t('my.set')" bgColor="#14142B" leftIconColor="#F585FF" autoBack fixed placeholder />
		<view class="warp">
			<u-form ref="formRef" class="sy-form" labelPosition="top" :labelStyle="labelStyle" labelWidth="100%" :model="model" >
				<u-form-item :label="$t('form.advar')">
					<view class="avatar-warp">
						<u-avatar class="avatar-item" @click="model.avatar_code = index+1" :class="{'u-border': model.avatar_code == index+1}" size="80rpx" v-for="(item,index) in avatarUrls" :key="index" :src="item"></u-avatar>
					</view>
				</u-form-item>
				<u-form-item :label="$t('form.nickname')">
					<u-input v-model="model.nickname" :placeholder="$t('form.nicknameMsg')" placeholderStyle="color:#707098" color="#707098"></u-input>
				</u-form-item>
				<u-form-item :label="$t('form.wechat')">
					<u-input v-model="model.wx_number" :placeholder="$t('form.wechatMsg')" placeholderStyle="color:#707098" color="#707098" ></u-input>
				</u-form-item>
				<u-form-item :label="$t('form.gender')" @click="genderShow = true">
					<u-input v-model="genderName" :placeholder="$t('form.genderMsg')" placeholderStyle="color:#707098" color="#707098" readonly suffixIcon="arrow-down-fill"></u-input>
				</u-form-item>
				<u-form-item :label="$t('form.address')">
					<u-input v-model="model.province + model.city"disabled  :placeholder="$t('form.addressMsg')" placeholderStyle="color:#707098" color="#707098" style="background-color:#323256"></u-input>
				</u-form-item>
				<template v-if="accountType=='mobile'">
					<u-form-item :label="$t('form.phone')">
						<u-input v-model="model.account" :placeholder="$t('form.phoneMsg')" placeholderStyle="color:#707098" color="#707098"></u-input>
					</u-form-item>
					<u-form-item :label="$t('form.email')">
						<u-input v-model="model.other_account" :placeholder="$t('form.emailMsg')" placeholderStyle="color:#707098" color="#707098"></u-input>
					</u-form-item>	
				</template>
				<template v-else>
					<u-form-item :label="$t('form.phone')">
						<u-input v-model="model.other_account" :placeholder="$t('form.phoneMsg')" placeholderStyle="color:#707098" color="#707098"></u-input>
					</u-form-item>
					<u-form-item :label="$t('form.email')">
						<u-input v-model="model.account" :placeholder="$t('form.emailMsg')" placeholderStyle="color:#707098" color="#707098" ></u-input>
					</u-form-item>
				</template>
				<u-form-item :label="$t('邀请码')" v-show="!model.invite_code">
					<u-input v-model="model.invite_code" :placeholder="$t('请输入邀请码（选填）')" placeholderStyle="color:#707098" color="#707098" ></u-input>
				</u-form-item>
				<u-form-item :label="$t('form.enounce')">
					<u-textarea v-model="model.declaration" maxlength="15" :placeholder="$t('form.enounceMsg')" placeholderStyle="color:#707098" color="#707098"></u-textarea>
				</u-form-item>
			</u-form>
			<u-button customStyle="margin-top: 40rpx" size="large" :text="$t('confirm')" color="linear-gradient(to right, #9041FF, #E323FF)" @click="submitHandler"></u-button>			
		</view>
		<u-action-sheet :show="genderShow" :actions="genderActions" @close="genderShow = false" @select="selectGender" safeAreaInsetBottom round="16rpx" count ></u-action-sheet>
	</view>
</template>

<script>
	import { mapActions, mapState } from 'vuex';
	import { getUserInfoApi, updateUserInfoApi } from '@/api/user.js'
	export default {
		data() {
			return {
				labelStyle: {
					'margin-bottom': '16rpx',
					'font-size': '26rpx',
					'line-height': '36rpx',
					'color': '#FFFFFF'
				},
				avatarUrls: [
					'/static/img/user/icon1.png',
					'/static/img/user/icon2.png',
					'/static/img/user/icon3.png',
					'/static/img/user/icon4.png',
					'/static/img/user/icon5.png',
				],
				model: {
					avatar_code: 0,
					nickname: '',
					wx_number: '',
					gender: '',
					region: '',
					account: '',
					other_account: '',
					declaration: '',
					city:'',
					province:'',
					invite_code:''
				},
				genderName: '',
				genderShow: false,
				genderActions: []
			}
		},
		computed: {
			accountType() {
				let { account } = this.model;
				return isNaN(account)?'email':'mobile';
			},
			...mapState({
				locale: state => state.locale
			}),
			localeIndex() {
				return this.locale == 'zh-cn'? 1 : 2;
			},
		},
		methods: {
			...mapActions(['initUserInfo']),
			init() {
				console.log(this.locale)
				getUserInfoApi().then(({code, data, msg}) => {
					let genderNames = this.locale == 'zh-cn'? ['保密', '男', '女'] : ['secrecy', 'male', 'female'];
					this.genderActions = this.locale == 'zh-cn'? [
						{ key: 1, name: '男' },
						{ key: 2, name: '女' },
						{ key: 0, name: '保密' },
					] : [
						{ key: 1, name: 'male' },
						{ key: 2, name: 'female' },
						{ key: 0, name: 'secrecy' },
					];
					let { account, avatar_code, nickname, wx_number, gender, other_account, declaration, province, city ,invite_code } = data;
					Object.assign(this.model, { avatar_code, nickname, wx_number, gender, account, other_account, declaration,province, city,invite_code });
					this.genderName = genderNames[gender];
				})
			},
			selectGender (e) {
				let { key, name } = e;
				this.model.gender = key;
				this.genderName = name;
			},
			submitHandler () {
				updateUserInfoApi(this.model).then(({code, msg}) => {
					msg && uni.$u.toast(msg);
					if (code == 1) {
						this.initUserInfo();
						uni.reLaunch({url: '/pages/index/index?tab=my'});
					}
				})
			}
		},
		created() {
			this.init()
		}
	}
</script>

<style lang="scss" scoped>
	.setting {
		.warp {
			padding-top: 170rpx;
			padding-bottom: 60rpx;
			.avatar-warp {
				display: flex;
				align-items: center;
				.avatar-item {
					margin-right: 32rpx;
					&.u-border {
						border-color: #FFFFFF!important;
					}
				}
			}
		}
	}
</style>
