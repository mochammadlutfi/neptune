<template>
    <el-card title="Activity">
        
    </el-card>
</template>


<script setup>
import { ref, watch, onMounted } from 'vue';
import axios from 'axios';
import { useQuery } from '@tanstack/vue-query';

const props = defineProps({
    subject :{
        type : String,
        default : ''
    }
})

const queryParams = reactive({
    subject_type : props.subject,
    per_page: 25,
    page: 1,
    q: "",
    sort: 'contract_number',
    sortDir: 'asc',
})

const fetchData = async ({ queryKey }) => {
  const [_key, queryParams] = queryKey;
  const response = await axios.get("/activity", {
      params: queryParams,
  });
  return response.data;
};

const { data, isLoading, isError, error, refetch } = useQuery({
  queryKey: [`activityList${props.subject}`, queryParams],
  queryFn: fetchData,
  keepPreviousData: true,
});

onMounted(() => {
    refetch
})

</script>