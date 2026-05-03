<script setup lang="ts">
	import { ref, reactive, inject, onMounted } from 'vue';
	import { fetchWithCsrf } from '@/main';
	import LeadCategory from '@classes/LeadCategory';
	
	const emit = defineEmits({
		'edit-category': (category: LeadCategory) => {
			return true;
		},
	});
	
	const props = defineProps({
		category: {
			type:		LeadCategory,
			required:	true,
		},
		csrfToken: {
			type:		String,
			required:	true,
		},
	});
	
	onMounted(async () => {
		try {
			const result = await fetchWithCsrf('/api/v1/system-admin/lead-category/by-parent-id/');
		} catch (error) {
			
		}
	});
	
</script>

<template>
	<div class="lead-category">
		<div class="label">{{ category.label }}</div>
		<div class="level-toggle"></div>
		<div class="edit"><a @click.prevent="$emit('edit-category', props.category)">Edit</a></div>
		<div class="children">
			<LeadCategoryTreeItem v-if="props.category.children?.length" v-for="child in props.category.children" :key="child.id ?? undefined" />
		</div>
	</div>
</template>

<style scoped>

</style>

