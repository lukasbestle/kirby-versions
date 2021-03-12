<template>
  <div class="lbvs-version">
    <span class="lbvs-version-header">
      <strong>{{ version.label }}</strong>
      
      <template v-if="instances">
        <lbvs-instance-name
          v-for="instance in version.instances"
          :key="instance"
          :inline="true"
          :instance="$store.state.versions.data.instances[instance]"
        />
      </template>
    </span>

    <dl class="lbvs-version-details">
      <template v-for="detail in mergedDetails">
        <dt :key="detail.title" class="k-offscreen">
          {{ detail.title }}:
        </dt>
        <dd :key="detail.title" :title="detail.title">
          {{ detail.value }}
        </dd>
      </template>
    </dl>
  </div>
</template>

<script>
export default {
  props: {
    details: {
      type: Array,
      default() {
        return [];
      }
    },
    instances: Boolean,
    version: Object
  },
  computed: {
    mergedDetails() {
      let details = [
        {
          title: this.$t("versions.label.versionName"),
          value: this.version.name
        },
        ...this.details
      ];
      
      return details.filter(detail => detail.value);
    }
  }
};
</script>

<style>
.lbvs-version {
  line-height: 1.4;
}

.lbvs-version-header {
  display: block;
}

.lbvs-version .lbvs-instance-name {
  margin-right: 0.3em;
}

.lbvs-version-details {
  font-size: var(--font-size-small);
}

.lbvs-version-details dd {
  display: inline;
}
.lbvs-version-details dd:not(:last-child)::after {
  content: " · ";

  color: var(--color-text-light);
}
</style>
