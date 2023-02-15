<template>
	<view class="comment-bar">
		<view class="operate">
			<u-icon v-if="!hideZan" style="margin-right: 36rpx;" :color="give?'#F585FF':'rgba(255,255,255,0.6)'" size="36rpx" name="thumb-up-fill" @click="giveHandler"></u-icon>
			<u-icon style="margin-right: 36rpx;" :color="collect?'#F585FF':'rgba(255,255,255,0.6)'" size="36rpx" name="heart-fill" @click="collectHandler"></u-icon>
		</view>	
		<u-input style="padding: 0 24rpx;margin-right: 8rpx;" v-model="content" :placeholder="$t('comment.placeholder')" color="#ffffff" shape="circle" border="none"/>
		<u-button style="width: 40rpx;color: #F585FF;margin: auto;" size="mini" :text="$t('confirm')" color="rgba(245, 133, 255, 0.3)" border="none" @click="commentHandler"></u-button>
	</view>
</template>

<script>
	import { mapState } from 'vuex';
	import { sendArticleActionApi, sendArticleCommentApi } from '@/api/index.js';
	export default {
		name:"comment-bar",
		data() {
			return {
				content: ''
			};
		},
		props:["sendComment","sendAction","id","type","give","collect","hideZan"],
		methods: {
			giveHandler() {
				const params = {
					type: 2,
					action: this.give? 0: 1
				};
				Object.assign(params,this.type == 'Information'?{information_id: this.id}:{knowledge_id: this.id})
				this.action(params);
			},
			collectHandler() {
				const params = {
					type: 1,
					action: this.collect? 0: 1
				};
				Object.assign(params,this.type == 'Information'?{information_id: this.id}:{knowledge_id: this.id})
				this.action(params);
			},
			action(params) {
				if(!this.token) {
					uni.$u.toast(this.$t('loginMsg'));
				} else {
					if(this.sendAction){
						this.sendAction()
						return
					}
					sendArticleActionApi(this.type, params).then(({code, msg}) => {
						if (code == 1) {
							this.$emit('success');
						}
						msg && uni.$u.toast(msg);
					});
				}
			},
			commentHandler(value) {
				if (!this.content) return;
				if (!this.token) {
					uni.$u.toast(this.$t('loginMsg'))
					return;
				}
				if(this.sendComment){
					this.sendComment(this.content)
					this.content = '';
					return
				}
				const params = Object.assign({content: this.content }, this.type == 'Information'?{information_id: this.id}:{knowledge_id: this.id});
				sendArticleCommentApi(this.type, params).then(({code, msg}) => {
					if (code == 1) {
						this.$emit('success');
						this.content = '';
					}
					msg && uni.$u.toast(msg);
				})
			}
		},
		computed: {
			...mapState({
				token: state => state.token
			}),
		}
	}
</script>

<style lang="scss" scoped>
	.comment-bar {
		position: fixed;
		left: 0;
		right: 0;
		bottom: 0;
		z-index: 99;
		display: flex;
		justify-content: space-between;
		height: 64rpx;
		padding: 16rpx 40rpx 56rpx;
		background-color: #232344;
		.operate {
			display: flex;
			align-items: center;
		}
	}
</style>
