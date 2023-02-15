<template>
	<view>
		<u-navbar :title="$t('编辑商家')" bgColor="#14142B" leftIconColor="#F585FF" autoBack />
		<view class="my-page">
			<u-form labelPosition="top" labelWidth="400">
				<u-form-item :label="$t('商家名称(10字以内)')">
					<u-input v-model="form.name" color="#FFF"  :placeholder="$t('请输入商家名称')"></u-input>
				</u-form-item>
				<u-form-item :label="$t('商家联系方式')">
					<u-input v-model="form.phone" color="#FFF"  :placeholder="$t('请输入商家联系方式')"></u-input>
				</u-form-item>
				<u-form-item :label="$t('所在地区')">
					<u-input v-model="form.address" color="#FFF"  :placeholder="$t('请输入地区')"></u-input>
				</u-form-item>
				<u-form-item :label="$t('商家简介')">
					<u--textarea v-model="form.describe" color="#FFF" :placeholder="$t('请输入商品简介')"></u--textarea>
				</u-form-item>
			</u-form>
			<view class="pop-btn-wapper">
				<view @click="reset">{{$t('重置')}}</view>
				<view @click="submit">{{$t('确认提交信息')}}</view>
			</view>
		</view>

	</view>
</template>

<script>
	import {MerchantUpMerchant} from '@/api/index.js'
	export default {
		data() {
			return {
				id:"",
				form: {
					name:"",
					phone:"",
					address:"",
					describe:"",
					province:"--",
					city:"--",
					area:"--"
				}
			}
		},
		onLoad({id}) {
			this.id = id
		},
		methods: {
			reset(){
				this.form = {
					name:"",
					phone:"",
					address:"",
					describe:"",
					province:"--",
					city:"--",
					area:"--"
				}
			},
			async submit(){
				let res = await MerchantUpMerchant({
					...this.form,id:this.id
				})
				if(res.code==1){
					uni.showToast({
						icon:"none",
						title:res.msg
					})
					setTimeout(()=>{
						uni.navigateBack()
					},1000)
				}
			}
		}
	}
</script>

<style scoped>
	.my-page {
		padding: 0 30rpx;
	}

	.pop-btn-wapper {
		background: #323256;
		display: flex;
		margin-bottom: 40rpx;
		height: 100rpx;
		border-radius: 10rpx;
		overflow: hidden;
		margin-top: 50rpx;
	}

	.pop-btn-wapper view {
		height: 100%;
		display: flex;
		align-items: center;
		justify-content: center;
		flex: 1;
		font-size: 28rpx;
		color: #FFF;
	}

	.pop-btn-wapper view:last-child {
		background: linear-gradient(266deg, #e323ff 2%, #9041ff 92%);
	}
</style>
