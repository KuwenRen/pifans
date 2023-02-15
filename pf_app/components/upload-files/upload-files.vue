<!-- 计数的图片上传 -->
<template>
	<view class="upload-files">
		<view class="list">
			<block v-for="(item,key) in images" :key="key">
				<view class="i">
					<text class="icon iconfont" @click="deleteClick(key)">X</text>
					<!-- 视频 -->
					<template v-if="status==1">
						<image class="img" :src="item" mode="aspectFill"></image>
						<view class="play-box">
							<image class="play" src="/static/images/play.png" @click="saveplay(item.vedio_url)">
							</image>
						</view>
					</template>
					<!-- 图片 -->
					<template v-else>
						<image class="img" :src="item" mode="aspectFill" @click="prevImages(item)"></image>
					</template>
				</view>
			</block>
			<template v-if="status==1&&!videoUrl">
				<view v-if="count<1" class="add" @click="uploadVideo">
					<text class="iconfont">&#xe600;</text>
					<text class="tips">{{$t('上传视频')}}({{count}}/1)</text>
				</view>
			</template>
			<template v-if="status==0&&count-images.length>0">
				<view class="add" @click="uploadImage">
					<text class="iconfont">+</text>
					<text class="tips">{{$t('上传图片')}}({{images.length}}/{{count}})</text>
				</view>
			</template>
		</view>
	</view>
</template>

<script>
	import file from '@/api/file'
	export default {
		props: {
			status: { //0上传图片，1上传视频
				type: Number,
				default: 0
			},
			list: Array,
			count: { //最大上传数量
				type: Number,
				default: 9
			},
			// baseUrl: 'https://www.pifan.club/api/file'
			baseUrl: 'https://pifans.app/api/file'
		},
		data() {
			return {
				images: [] //列表
			}
		},
		watch: {
			list: function(arr) {
				this.images = arr.map(item => {
					return this.$store.state.apiUrl + item
				})
			}
		},
		methods: {
			// 删除
			deleteClick: function(i) {
				this.images.splice(i, 1);
				this.$emit('deleteClick', i);
			},
			// 播放视频
			saveplay: function(url) {
				this.$emit('saveplay', url);
			},
			// 图片列表
			prevImages: function(current) {
				uni.previewImage({
					current: current,
					urls: this.images
				})
			},
			// 上传视频
			uploadVideo: function() {
				uni.chooseVideo({
					success: (res) => {
						file.uploadFile({
							file: res.tempFilePath
						}).then(res => {
							this.images = [res.data.filename];
							this.$emit('uploadVideo', ref.data.url);
						})
					}
				})
			},
			// 上传图片
			uploadImage: function() {
				let count = this.count - this.images.length;
				uni.chooseImage({
					count: count,
					success: (res) => {
						res.tempFilePaths.forEach(item => {
							file.uploadFile({
								file: item
							}).then(ref => {
								this.images.push(ref.data);
								this.$emit('uploadImage', ref.data);
							})
						})
					}
				})
			}
		}
	}
</script>

<style lang="scss" scoped>
	.upload-files {
		width: 100%;
	}

	.list {
		width: 100%;
		display: flex;
		justify-content: space-between;
		flex-wrap: wrap;

		.i {
			position: relative;
			margin-bottom: 20rpx;
			width: 200rpx;
			height: 200rpx;
			border-radius: 10rpx;
			overflow: hidden;
			display: flex;
			align-items: center;
			justify-content: center;

			.img {
				width: 200rpx;
				height: 200rpx;
			}

			.icon {
				position: absolute;
				z-index: 9;
				top: 2px;
				right: 2px;
				color: #FF0000;
				font-size: 36rpx;
			}
		}

		.add {
			margin-bottom: 20rpx;
			width: 200rpx;
			height: 200rpx;
			box-sizing: border-box;
			border: 1px solid #dddddd;
			border-radius: 10rpx;
			display: flex;
			align-items: center;
			justify-content: center;
			flex-direction: column;

			.iconfont {
				color: #999999;
				font-size: 60px;
			}

			.tips {
				color: #999999;
				font-size: 20rpx;
			}
		}
	}

	.play-box {
		padding: 10rpx;
		position: absolute;
		z-index: 9;
		display: flex;
		align-items: center;
		justify-content: center;
		border: 10rpx solid #FFFFFF;
		border-radius: 50%;

		.play {
			width: 50rpx;
			height: 50rpx;
			transform: rotate(90deg);
		}
	}

	.list:after {
		content: '';
		width: 200rpx;
	}
</style>
