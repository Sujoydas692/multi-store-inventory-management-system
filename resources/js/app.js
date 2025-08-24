import './bootstrap';

axios.interceptors.response.use(
  r => r,
  err => {
    if (err.response && err.response.status === 401) {
      localStorage.removeItem('token');
      document.cookie = 'token=; expires=Thu, 01 Jan 1970 00:00:00 GMT; path=/;';
      window.location.href = '/login';
    }
    return Promise.reject(err);
  }
);


