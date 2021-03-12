<template>
  <div class="lbvs-versions">
    <k-view>
      <header class="k-section-header">
        <k-headline>
          {{ $t("versions.label.versions") }}
        </k-headline>
      </header>

      <k-list v-if="items.length">
        <k-list-item
          v-for="(item, index) in items"
          :key="index"
          v-bind="item"
          :image="true"
          :options="options(item)"
          @action="onOption($event, item)"
        >
          <template #image>
            {{ index + 1 }}
          </template>

          <lbvs-version
            :details="versionDetails(item)"
            :instances="true"
            :version="item"
          />
        </k-list-item>
      </k-list>

      <k-empty v-else layout="cards">
        {{ $t("versions.label.empty") }}
      </k-empty>
    </k-view>

    <lbvs-export-dialog ref="exportDialog" />
    <lbvs-deploy-dialog ref="deployDialog" />
    <lbvs-delete-dialog ref="deleteDialog" />
  </div>
</template>

<script>
export default {
  computed: {
    items() {
      return Object.values(this.$store.state.versions.data.versions);
    }
  },
  methods: {
    onOption(option, version) {
      return this.$refs[option + "Dialog"].open(version);
    },
    options(version) {
      let permissions = this.$permissions["lukasbestle.versions"];

      return [
        {
          click: "export",
          disabled: permissions.export !== true,
          icon: "download",
          text: this.$t("versions.button.export")
        },
        {
          click: "deploy",
          disabled: permissions.deploy !== true,
          icon: "wand",
          text: this.$t("versions.button.deploy")
        },
        {
          click: "delete",
          disabled: permissions.delete !== true || version.instances.length > 0,
          icon: "trash",
          text: this.$t("versions.button.delete")
        }
      ];
    },
    versionDetails(version) {
      return [
        {
          title: this.$t("versions.label.creation"),
          value: this.versionDetailsToString("creationData", {
            created: (
              version.created ?
                this.$library.dayjs.unix(version.created).format("YYYY-MM-DD HH:mm") :
                "?"
            ),
            creator: version.creatorName || version.creatorEmail || "?"
          })
        },
        {
          title: this.$t("versions.label.originInstance"),
          value: this.versionDetailsToString("from", {
            originInstance: version.originInstance || "?"
          })
        }
      ];
    },
    versionDetailsToString(key, data) {
      // no need to create a string if we don't have data
      if (Object.values(data).every(value => value === "?") === true) {
        return null;
      }

      return this.$t("versions.label." + key, data);
    }
  }
};
</script>

<style>
.lbvs-versions {
  padding-top: 1.5rem;
}

.lbvs-versions .k-list-item-image {
  font-size: var(--font-size-small);
  line-height: 38px;
  text-align: center;

  color: var(--color-text-light);
}
 
.lbvs-versions .k-list-item-text {
  white-space: initial;
}
</style>
