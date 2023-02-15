<template>
    <view class="label container">
        <view class="warp">
			<view class="icon-wapper">
				<u-icon @click="back" name="arrow-left" color="#FFF"></u-icon>
			</view>
        	<u-search color="#ffffff" bgColor="#323256" searchIconColor="#C4C4C4" borderColor="#404065" placeholderColor="rgba(255,255,255,0.8)"
        		:placeholder="$t('searchPlace')" height="80" shape="square" :showAction="false" v-model="search" border-color @search="searchHnadler" @clear="clear" @change="changes"/>
        	
        </view>
		<view class="label-list" :style="`width:${size}px;height:${size}px`" >
			<view
				class="lable-item"
				v-for="(tag, index) in tags"
				:key="index"
				:style="`top: ${tag.y}px;left: ${tag.x}px;font-size: ${20 * (600/(600-tag.z))}px; color: ${tag.color};opacity: ${((400+tag.z)/600)}`"
				@click="show(tag.extData)"
			>{{tag.text}}</view>
		</view>
		<view class="label-banner" v-if="curExtData">
			<image class="label-banner__avatar" width="148rpx" height="148rpx" :src="`../../static/img/user/icon${curExtData.avatar_code}.png`" mode=""></image>
			<view class="label-banner__info">
				<u-text lines="1" :text="curExtData.nickname" color="#EE95FD" size="36rpx" lineHeight="50rpx" blod></u-text>
				<u-text margin="8rpx 0" lines="1" :text="curExtData.declaration" color="#FFFFFF" size="32rpx" lineHeight="44rpx" ></u-text>
				<view class="desc">
					<view>{{curExtData.wx_number}}</view>
					<view>{{curExtData.address}}</view>
				</view>
			</view>
		</view>
    </view>
</template>

<script>
	import { getLabelList } from '@/api/index.js'
	export default {
		data() {
			return {
				size: undefined,
				search: '',
				curExtData: null,
				tags: [],
				speedX: Math.PI/360, //球一帧绕x轴旋转的角度
				speedY: Math.PI/360, //球-帧绕y轴旋转的角度
			}
		},
		methods: {
			back(){
				uni.navigateBack()
			},
			searchHnadler() {
				this.init();
			},
			clear(){
				this.init();
			},
			changes(e){
				if(this.search==''){
					this.init();
				}
			},
			init() {
				let { radius, qX, qY, search } = this;
				
				getLabelList({ search }).then(({code, data, msg}) => {
					const length = data.length;
							
					this.tags = data.map((v, i) => {
						let k = -1 + (2 * (i + 1) - 1) / length;
						let a = Math.acos(k);
						let b = a * Math.sqrt(length * Math.PI)//计算标签相对于球心的角度
						let text = v.nickname;
						let x = qX +  radius * Math.sin(a) * Math.cos(b);//根据标签角度求出标签的x,y,z坐标
						let y = qY +  radius * Math.sin(a) * Math.sin(b); 
						let z = radius * Math.cos(a);
						return { text, x, y, z, color: this.initColor(), extData: v };
					});
				});
			},
			initColor() {
				const arr = ['#C42FFF', '#2577FF', '#FFB910', '#3EC2D9', '#F47751']
				const index = Math.floor(Math.random()*5); 
				return arr[index];//所有方法的拼接都可以用ES6新特性`其他字符串{$变量名}`替换
			},
			rotateX(angleX) {
				let { qY, tags } = this;
				let cos = Math.cos(angleX);
				let sin = Math.sin(angleX);
				for(let tag of tags){
					var y1 = (tag.y- qY) * cos - tag.z * sin  + qY;
					var z1 = tag.z * cos + (tag.y - qY) * sin;
					tag.y = y1;
					tag.z = z1;
				}
			},
			rotateY(angleY) {
				let { qX, tags } = this;
				var cos = Math.cos(angleY);
				var sin = Math.sin(angleY);
				for(let tag of tags){
					var x1 = (tag.x - qX) * cos - tag.z * sin + qX;
					var z1 = tag.z * cos + (tag.x - qX) * sin;
					tag.x = x1;
					tag.z = z1;
				}
			},
			rotate(flag) {
				const self = this;
				self.curExtData = null;
				if (flag) {
					self.timer = setInterval(() => {
						self.rotateX(self.speedX);
						self.rotateY(self.speedY);
					}, 17)
				} else {
					clearInterval(self.timer);
				}
			},
			show(record) {
				this.curExtData = record;
			}
		},
		computed: {
			radius() {
				return this.size? this.size/2 : 0;
			},
			qX() {
				return this.size? this.size/2: 0;
			},
			qY() {
				return this.size? this.size/2 : 0;
			}
		},
		created() {
			const { windowWidth, windowHeight } = uni.getSystemInfoSync();
			this.size = windowWidth;
			this.init();
		},
		mounted() {
			this.rotate(true);
		}
	}
</script>

<style lang="scss" scoped>
	.icon-wapper{
		margin-right: 30rpx;
	}
    .label {
		padding-top: var(--status-bar-height);
		.warp {
			padding-top: 64rpx;
			display: flex;
			// flex-direction: column;
			justify-content: center;
			align-items: center;
			color: #FFF;
		}
        .label-header {
            display: flex;
        }
        .label-list {
			position: relative;
            margin-top: 60px;
			overflow: hidden;
			.lable-item {
				position: absolute;
				width: fit-content;
			}
        }

		.label-banner {
			padding: 0 40rpx;
			display: flex;
			align-items: center;
			position: fixed;
			left: 0;
			bottom: 140rpx;
			width: 100%;
			height: 218rpx;
			background: url(../../static/img/map-banner.png) no-repeat;
			background-size: 100% 100%;
			box-sizing: border-box;
			.label-banner__avatar {
				width: 148rpx;
				height: 148rpx;
				border-radius: 50%;
				border: 1px solid #FFFFFF;
				margin-right: 24rpx;
			}
			.label-banner__info {
				.desc {
					display: flex;
					color: rgba(255,255,255,0.6);
					font-size: 28rpx;
					line-height: 40rpx;
				}
			}
		}
    }
</style>
