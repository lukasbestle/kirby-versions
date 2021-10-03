<template>
  <k-inside>
    <div class="lbvs-view">
      <k-loader v-if="isLoading" />
      <template v-else>
        <lbvs-status />
        <lbvs-versions />
      </template>
    </div>
  </k-inside>
</template>

<script>
export default {
  data() {
    return {
      isLoading: true
    };
  },
  async mounted() {
    if (this.$permissions["lukasbestle.versions"].access !== true) {
      this.$go("/");
    }

    try {
      this.isLoading = true;
      await this.$store.dispatch("versions/load");
    } finally {
      this.isLoading = false;
    }
  }
};
</script>

<style>
@import '../index.css';

.lbvs-view > .k-loader {
  position: absolute;
  top: 50%;
  inset-inline-start: 50%;
  transform: translate(-50%, -50%);
}
</style>
