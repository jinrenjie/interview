<script setup>
import {ref, reactive, computed} from "vue";
import "codemirror/mode/sql/sql.js"
import { format } from 'sql-formatter';
import {ElMessage} from "element-plus";
import Codemirror from "codemirror-editor-vue3";
import {Head, router, usePage} from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

const cmRef = ref();
const sql = ref("");

const page = usePage();
const result = ref(page.props.result ?? []);
const columns = ref(page.props.columns ?? []);

const query = reactive({
  page: 1,
  limit: 10,
})


const cmOptions = {
  mode: "text/x-sql",
  lint: true,
  gutters: ["CodeMirror-lint-markers"],
};

const onChange = (val, cm) => {
  console.log(val);
  console.log(cm.getValue());
};

const onInput = (val) => {
  console.log(val);
};

const onReady = (cm) => {
  console.log(cm.focus());
};

const formatSQL = () => {
  sql.value = format(sql.value, {language: 'mysql'});
}

const executeSQL = () => {
  router.post('/dev', {sql: sql.value}, {
    onSuccess: (res) => {
      result.value = res.props.result;
      columns.value = res.props.columns;
    },
    onError: (err) => {
      console.log(err);
      ElMessage.error(Object.values(err)[0]);
    }
  })
}

const paginateHandler = computed(() => {
  return result.value.slice((query.page - 1) * query.limit, query.page * query.limit)
})

/**
 * The table page size change handler.
 * @param size
 */
const handleSizeChange = (size) => {
  query.limit = size;
}

/**
 * The table page change handler.
 * @param page
 */
const handleCurrentChange = (page) => {
  query.page = page;
}
</script>

<template>
  <Head title="Dashboard" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">SQL Executor</h2>
    </template>
    <div class="py-6">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="p-4 bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="grid grid-cols-1 divide-y">
            <div class="mb-2">
              <el-row>
                <el-col :span="24">
                  <Codemirror class="sql-code font-sans rounded border-slate-100" v-model:value="sql"
                              :options="cmOptions" border ref="cmRef" height="100"
                              @change="onChange" @input="onInput" @ready="onReady"
                  >
                  </Codemirror>
                </el-col>
              </el-row>
            </div>
            <div class="pt-4">
              <el-row :gutter="20">
                <el-col :span="12"></el-col>
                <el-col :span="12" class="text-right">
                  <el-button size="large" plain @click="formatSQL">Format</el-button>
                  <el-button type="primary" size="large" plain @click="executeSQL">Execute</el-button>
                </el-col>
              </el-row>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="py-4">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="p-4 bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="grid grid-cols-1 divide-y">
            <div v-if="columns">
              <el-table size="large" :data="paginateHandler" style="width: 100%">
                <el-table-column v-for="(column, index) in columns" :key="index" :prop="column.prop" :label="column.label" />
              </el-table>
              <div class="mt-4 float-right">
                <el-pagination v-model:current-page="query.page" v-model:page-size="query.limit"
                               :total="result.length"
                               :disabled="false"
                               :background="false"
                               :page-sizes="[10, 50, 100]"
                               layout="total, sizes, prev, pager, next, jumper"
                               @size-change="handleSizeChange"
                               @current-change="handleCurrentChange"
                />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<style scoped>
.sql-code {
  font-size: 14px;
  font-family: monospace;
}
</style>