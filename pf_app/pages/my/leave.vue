<template>
	<view class="leave container" >
		<u-navbar :title="$t('leave.title')" bgColor="#14142B" leftIconColor="#F585FF" autoBack fixed placeholder />
		<view class="sy-header">
			<view class="sy-header__inner">{{$t('leave.title')}}</view>
		</view>
		<view class="leave-msg">{{$t('leave.msg')}}</view>
		<u--textarea class="leave-input" v-model="content" :placeholder="$t('leave.placeholder')" :placeholderStyle="{color: '#707098'}" height="400rpx" maxlength="200" ></u--textarea>
		<u-upload @afterRead="uploadHandler" :maxCount="1">
			<view class="leave-upload" >
				<u-image v-if="image" width="164rpx" height="164rpx" :src="baseUrl+image" radius="8rpx"></u-image>
				<template v-else>
					<u-icon name="photo-fill" color="#707098" size="60rpx"></u-icon>
					<view style="margin-top: 22rpx; font-size:26rpx; line-height: 1; color: #707098;">{{$t('leave.upload')}}</view>
				</template>
			</view>
		</u-upload>
		<u-button customStyle="margin-top: 40rpx" :text="$t('submit')" color="linear-gradient(to right, #9041FF, #E323FF)" @click="submit" size="large"></u-button>
	</view>
</template>

<script>
	import { mapState } from 'vuex';
	import { baseUrl } from '@/api/http.js';
	import { sendLeaveMessageApi } from '@/api/index.js';
	export default {
		data() {
			return {
				baseUrl,
				content: '',
				image: ''
			}
		},
		methods: {
			uploadHandler(event) {
				let { file } = event;
				uni.uploadFile({
					url: baseUrl+'/api/common/upload', // 仅为示例，非真实的接口地址
					filePath: file.url,
					name: 'file',
					success: ({statusCode, data, errMsg}) => {
						const result = JSON.parse(data);
						let { code, data: url, msg } = result;
						if(code == 1) this.image = url;
						msg && uni.$u.toast(msg)
					},
					fail: (err) => {
						console.log(err)
						// uni.$u.toast(msg)
					}
				});
			},
			submit() {
				let { content, image, token } = this;
				if(!token) {
					uni.$u.toast(this.$t('loginMsg'))
					return;
				}
				if (!content) {
					uni.$u.toast(this.$t('leave.placeholder'))
				} else {
					sendLeaveMessageApi({ content, image }).then(({code, msg}) => {
						if(code == 1) {
							uni.navigateBack({ delta: 1 });
						}
						msg && uni.$u.toast(msg)
					})
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
		}
	}
</script>

<style lang="scss" scoped>
	.leave {
		padding-top: 170rpx;
		padding-left: 40rpx;
		padding-right: 40rpx;
		padding-bottom: 60rpx;
		box-sizing: border-box;
		.sy-header {
			margin-bottom: 32rpx;
		}
		.leave-msg {
			margin-bottom: 32rpx;
			font-size: 26rpx;
			line-height: 36rpx;
			color: rgba(255,255,255,0.6);
		}
		.leave-input {
			border-color: transparent!important;
		}
		.leave-upload {
			margin-top: 32rpx;
			display: flex;
			flex-direction: column;
			justify-content: center;
			align-items: center;
			width: 164rpx;
			height: 164rpx;
			border-radius: 8rpx;
			background-color: #323256;
		}
	}
</style>
