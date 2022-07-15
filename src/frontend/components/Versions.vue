<template>
	<div class="lbvs-versions">
		<k-view>
			<header class="k-section-header">
				<k-headline>
					{{ $t("versions.label.versions") }}
				</k-headline>
			</header>

			<k-items
				v-if="items.length"
				:columns="columns"
				:items="items"
				layout="table"
				:sortable="false"
				@option="onOption"
			/>

			<k-empty v-else icon="protected" layout="table">
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
		columns() {
			return {
				title: {
					label: this.$t("versions.label.label"),
					type: "lbvs-version-label",
					mobile: true,
					width: "35%"
				},
				instances: {
					label: this.$t("versions.label.instances"),
					type: "lbvs-instance-names",
					mobile: true,
					width: "25%"
				},
				creation: {
					label: this.$t("versions.label.creation"),
					type: "text",
					width: "25%"
				},
				originInstance: {
					label: this.$t("versions.label.originInstance"),
					type: "lbvs-instance-names",
					width: "15%"
				}
			};
		},
		items() {
			return Object.values(this.$store.state.versions.data.versions).map(
				(version) => {
					// assemble combined fields for the table layout
					version.creation = this.$t("versions.label.creationData", {
						created: version.created
							? this.$library.dayjs
									.unix(version.created)
									.format("YYYY-MM-DD HH:mm")
							: "?",
						creator: version.creatorName || version.creatorEmail || "?"
					});

					version.title = {
						label: version.label,
						name: version.name
					};

					// populate options dropdown of this row
					version.options = this.options(version);

					return version;
				}
			);
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
		}
	}
};
</script>

<style>
.lbvs-versions {
	padding-top: 1.5rem;
}
</style>
