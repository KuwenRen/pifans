<template>
	<view>
		<u-navbar :title="$t('评价')"  bgColor="#14142B" leftIconColor="#F585FF" autoBack></u-navbar>
		<view class="content">
			<u-form labelPosition="top" labelWidth="600">
				<u-form-item :label="$t('评分')">
					<u-rate size="26" :count="5" v-model="form.score" :allowHalf="true"  activeColor="#FFD422"></u-rate>
				</u-form-item>
				<u-form-item :label="$t('评论')">
					<u-textarea v-model="form.comment" :placeholder="$t('请输入评论')"></u-textarea>
				</u-form-item>
			</u-form>
			<view class="btn-all" @click="submit">
				{{$t('确认提交信息')}}
			</view>
		</view>
	</view>
</template>

<script>
	import { MerchantComment } from '@/api/index.js';
	export default {
		data() {
			return {
				id:"",
				form:{
					merchant_id:'',
					comment:'',
					score:5,
				}
			}
		},
		onLoad({id}) {
			this.form.merchant_id = id
		},
		methods: {
			async submit(){
				let res = await MerchantComment(this.form)
				if(res.code==1){
					uni.showToast({
						title:this.$t("提交成功")
					})
					setTimeout(()=>{
						uni.navigateBack()
					},1000)
				}
			}
		}
	}
</script>

<style>
.content{
	margin: 20rpx;
}
.btn-all{
	position: absolute;
	bottom: 50rpx;
	width: 670rpx;
}
</style>
