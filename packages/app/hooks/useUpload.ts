import Cookies from 'js-cookie'
import Vapor from 'laravel-vapor'

const useUpload = () => {

  const uploadFiles = async (image: File[]) => {
    const token = Cookies.get('XSRF-TOKEN')

    let upload

    if (image && (image[0] instanceof File)) {
      upload = await Vapor.store(image[0], {
        baseURL: process.env.NEXT_PUBLIC_API_ENDPOINT,
        options: {
          withCredentials: 'include',
        },
        headers: {
          'X-XSRF-TOKEN': token,
        },
      })
    }

    return upload
  }

  return { uploadFiles }
}

export default useUpload
