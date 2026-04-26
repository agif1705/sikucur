<div class="w-100 h-100" wire:ignore x-data="{
    videos: @js($uploadedVideos),
    currentIndex: 0,
    get currentVideo() {
      return this.videos[this.currentIndex] ?? null;
    },
    play() {
      this.$refs.video?.play().catch(() => {
        setTimeout(() => this.$refs.video?.play().catch(() => {}), 1000);
      });
    },
    next() {
      this.currentIndex = (this.currentIndex + 1) % this.videos.length;
      this.$nextTick(() => {
        this.$refs.video.load();
        this.play();
      });
    },
  }" x-init="$nextTick(() => play())">
  <video x-ref="video" class="w-100 h-100" style="object-fit: contain;" autoplay muted playsinline controls
    preload="auto" x-bind:src="currentVideo?.url" x-bind:title="currentVideo?.title" @loadedmetadata="play"
    @canplay="play" @ended="next">
  </video>
</div>
