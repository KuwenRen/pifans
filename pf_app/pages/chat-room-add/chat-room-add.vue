<!-- 
@author x 
@title 聊天室添加
 -->
<template>
	<view class="chat-room-add">
		<u-header :title="$t('新增聊天室')"></u-header>
		<view class="form">
			<view class="item">
				<text>{{$t('聊天室名称')}}</text>
				<input class="input" v-model="formData.group_name" :placeholder="$t('请输入聊天室名称')" />
			</view>
			<view class="item">
				<text style="margin-bottom: 16px;">{{$t('聊天室封面图')}}</text>
				<upload-files :count="1" @deleteClick="deleteClick" @uploadImage="uploadImage"></upload-files>
			</view>
			<view class="item" @click="showBusiness_type=!showBusiness_type">
				<text>{{$t('聊天室类型')}}</text>
				<view class="i">
					<input class="input" v-model="business_type_name" disabled :placeholder="$t('请选择聊天室类型')" />
					<image v-if="showBusiness_type" class="icon" src="../../static/img/up.png" mode="widthFix"></image>
					<image v-else class="icon" src="../../static/img/down.png" mode="widthFix"></image>
					<category-popup v-if="showBusiness_type" :datalist="business_type" v-model="formData.group_type_id">
					</category-popup>
				</view>
			</view>
			<view class="item" @click="showswitch=!showswitch">
				<text>{{$t('是否仅群主/群管理员可修改群聊名称')}}</text>
				<view class="i">
					<input class="input" v-model="switch_name" disabled />
					<image v-if="showswitch" class="icon" src="../../static/img/up.png" mode="widthFix"></image>
					<image v-else class="icon" src="../../static/img/down.png" mode="widthFix"></image>
					<category-popup v-if="showswitch" :datalist="switch_type" v-model="formData.is_enable_switch">
					</category-popup>
				</view>
			</view>
			<u-button customStyle="margin-top: 32rpx" :text="$t('save')"
				color="linear-gradient(to right, #9041FF, #E323FF)" @click="submit"></u-button>
		</view>
	</view>
</template>

<script>
	import {
		typelist,
		addChatGroup
	} from '@/api/chat';
	import {
		mapState
	} from 'vuex';
	export default {
		data() {
			return {
				formData: {}, //表单
				business_type: [], //类别
				showBusiness_type: false,
				switch_type: [{
					id: 1,
					name: '是',
					name1: 'Yes'
				}, {
					id: 0,
					name: '否',
					name1: 'No'
				}], //类别
				showswitch: false,
			}
		},
		computed: {
			...mapState({
				locale: state => state.locale
			}),
			localeIndex() {
				return this.locale == 'zh-cn' ? 1 : 2;
			},
			/* 类型 */
			business_type_name() {
				let name = '';
				for (let item of this.business_type) {
					if (item.id == this.formData.group_type_id) {
						name = item.typename;
					}
				}
				return name
			},
			switch_name() {
				let name = '';
				for (let item of this.switch_type) {
					if (item.id == this.formData.is_enable_switch) {
						if (this.localeIndex == 1) {
							name = item.name;
						} else {
							name = item.name1;
						}

					}
				}
				return name
			},
		},
		onLoad() {
			Promise.all([this.typelist(), ]).then(() => {
				this.formData = this.resetFromData();
			})
		},
		methods: {
			/* 商家类别 */
			async typelist() {
				let res = await typelist();
				this.business_type = res.data.map(item => {
					item.checked = false;
					return item
				})
			},
			/* 删除 */
			deleteClick() {
				this.formData.show_image = '';
			},
			/* 图片上传 */
			uploadImage(url) {
				this.formData.show_image = url;
			},
			/* 生成表单 */
			resetFromData() {
				return {
					group_name: '', //聊天室名称
					group_type_id: '', //聊天室类型
					show_image: '', //聊天室封面
					is_enable_switch: 1 //是否仅群主/管理员修改群名称（1是，0否）
				}
			},
			/* 提交 */
			submit() {
				addChatGroup(this.formData).then(res => {
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
							uni.redirectTo({
								url: '/pages//chat-room/chat-room'
							})
						}, 800)
					}
				})
			},
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
