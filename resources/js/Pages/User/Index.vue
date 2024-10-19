<script setup>
import { reactive, ref } from 'vue'
import { Head } from '@inertiajs/vue3';
import { ElMessage } from 'element-plus'
import { Search } from '@element-plus/icons-vue'
import { router, usePage, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const page = usePage();
const users = page.props.users;
const roles = page.props.roles;

const params = new URLSearchParams(window.location.search);

const query = reactive({
  page: params.get('page') ? Number(params.get('page')) : 1,
  limit: params.get('limit') ? Number(params.get('limit')) : 10,
  search: params.get('search') ?? ''
})

const formLabelWidth = '50px';
const editUserDialogVisible = ref(false);

// Define update user payload
const user = useForm({
  id: 0,
  name: '',
  email: '',
  roles: []
});

/**
 * The table page size change handler.
 * @param size
 */
const handleSizeChange = (size) => {
  query.limit = size;
  searchUser();
}

/**
 * The table page change handler.
 * @param page
 */
const handleCurrentChange = (page) => {
  query.page = page;
  searchUser();
}

/**
 * Clear the search input handler.
 */
function clearSearch() {
  query.search = '';
  searchUser();
}

/**
 * Submit search form handler.
 */
function searchUser() {
  let queryParams = {
    page: query.page,
    limit: query.limit
  };

  if (query.search) {
    queryParams.search = query.search;
  }

  router.get('/users', queryParams);
}

function hiddenDialog() {
  editUserDialogVisible.value = false;
}

function editUserHandler(row) {
  user.id = row.id;
  user.name = row.name;
  user.email = row.email;
  user.roles = row.roles;
  editUserDialogVisible.value = true;
}

/**
 * Update user info handler.
 */
function updateUserHandler() {
  router.put(`/users/${user.id}`, {
    name: user.name,
    email: user.email,
    roles: user.roles
  }, {
    onSuccess: () => {
      hiddenDialog();
      searchUser();
    },
    onError: (err) => {
      ElMessage.error(Object.values(err)[0]);
    }
  });
}

/**
 * Localized time format
 * @param isoString
 * @returns {string}
 */
function formatISO8601(isoString) {
  const date = new Date(isoString);

  const year = date.getFullYear();
  const month = String(date.getMonth() + 1).padStart(2, '0');
  const day = String(date.getDate()).padStart(2, '0');

  const hours = String(date.getHours()).padStart(2, '0');
  const minutes = String(date.getMinutes()).padStart(2, '0');
  const seconds = String(date.getSeconds()).padStart(2, '0');

  return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
}
</script>

<template>
  <Head title="Dashboard" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Users</h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="p-4 bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="grid grid-cols-1 divide-y">
            <div class="mb-4">
              <el-row :gutter="20">
                <el-col :span="12">
                  <el-input v-model="query.search" style="width: 340px" size="large" placeholder="Search user by email"
                            :prefix-icon="Search" @clear="clearSearch" clearable/>
                </el-col>
                <el-col :span="12" class="text-right">
                  <el-button size="large" type="primary" @click="searchUser" plain>Search</el-button>
                </el-col>
              </el-row>
            </div>
            <div>
              <el-table size="large" :data="users.data" style="width: 100%">
                <el-table-column prop="id" label="ID" width="100" />
                <el-table-column prop="name" label="Name" width="120" />
                <el-table-column prop="email" label="Email" width="220" />
                <el-table-column prop="role" label="Role" min-width="200">
                  <template #default="scope">
                    <el-tag v-for="(role, index) in scope.row.roles" :key="index" size="large">{{ role }}</el-tag>
                  </template>
                </el-table-column>
                <el-table-column prop="created_at" label="Created At" min-width="180">
                  <template #default="scope">
                    <label>{{ formatISO8601(scope.row.created_at) }}</label>
                  </template>
                </el-table-column>
                <el-table-column prop="updated_at" label="Updated At" min-width="180">
                  <template #default="scope">
                    <label>{{ formatISO8601(scope.row.updated_at) }}</label>
                  </template>
                </el-table-column>
                <el-table-column label="Operations" width="120">
                  <template #default="scope">
                    <el-button link type="primary" size="large" @click="editUserHandler(scope.row)">Edit</el-button>
                  </template>
                </el-table-column>
              </el-table>
              <div class="mt-4 float-right">
                <el-pagination v-model:current-page="query.page" v-model:page-size="query.limit"
                               :total="users.total"
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

  <el-dialog v-model="editUserDialogVisible" title="Edit user" width="500">
    <el-form :model="user">
      <el-form-item label="Name" :label-width="formLabelWidth">
        <el-input v-model="user.name" autocomplete="off" />
      </el-form-item>
      <el-form-item label="Email" :label-width="formLabelWidth">
        <el-input v-model="user.email" autocomplete="off" />
      </el-form-item>
      <el-form-item label="Roles" :label-width="formLabelWidth">
        <el-select multiple v-model="user.roles" placeholder="Please select a zone">
          <el-option v-for="role in roles" :label="role.name" :value="role.name" />
        </el-select>
      </el-form-item>
    </el-form>
    <template #footer>
      <div class="dialog-footer">
        <el-button @click="editUserDialogVisible = false">Cancel</el-button>
        <el-button type="primary" @click="updateUserHandler">
          Confirm
        </el-button>
      </div>
    </template>
  </el-dialog>
</template>

<style scoped>

</style>