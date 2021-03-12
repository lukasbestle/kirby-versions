<template>
  <k-dialog
    ref="dialog"
    class="lbvs-version-dialog"
    :cancel-button="$t(data ? 'close' : 'cancel')"
    :submit-button="false"
  >
    <lbvs-version
      v-if="version"
      :details="details"
      :version="version"
    />

    <p v-if="!data">
      {{ $t("versions.message.exporting") }}
    </p>

    <k-button-group v-else>
      <k-button icon="download" @click="download">
        {{ $t("versions.button.download") }}
      </k-button>

      <k-button icon="copy" :disabled="!supportsClipboard" @click="copyToClipboard">
        {{ $t("versions.button.copyLink") }}
      </k-button>
    </k-button-group>
  </k-dialog>
</template>

<script>
export default {
  data() {
    return {
      data: null,
      version: {}
    };
  },
  computed: {
    details() {
      if (this.data) {
        return [
          {
            title: this.$t("versions.label.fileSize"),
            value: this.data.filesize
          }
        ];
      }

      return [];
    },
    supportsClipboard() {
      try {
        window.navigator.clipboard.writeText;
        return true;
      } catch (e) {
        return false;
      }
    }
  },
  methods: {
    async copyToClipboard() {
      await window.navigator.clipboard.writeText(this.data.url);

      this.$store.dispatch("notification/success", ":)");
    },
    download() {
      window.location = this.data.url;

      this.$store.dispatch("notification/success", ":)");
    },
    async open(version) {
      this.data = null;
      this.version = version;
      this.$refs.dialog.open();

      let data = await this.$store.dispatch({
        type: "versions/exportVersion",
        version: this.version.name
      });

      // only use the response if no other version
      // was requested in the meantime;
      // this drops outdated responses when the user
      // has aborted the request and initiated one
      // for a different version
      if (version === this.version) {
        this.data = data;
      }
    }
  }
};
</script>
