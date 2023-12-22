import { useDispatch, useSelector } from 'react-redux'
import { setSidebarOpen } from 'slices/sidebar'
import { RootState } from 'store'

const useSidebar = () => {
  const dispatch = useDispatch()

  const { isSidebarOpen } = useSelector((state: RootState) => ({
    isSidebarOpen: state.sidebar.isSidebarOpen,
  }))

  const setIsSidebarOpen = (isOpen: boolean) => dispatch(setSidebarOpen(isOpen))

  return { isSidebarOpen, setIsSidebarOpen }
}

export default useSidebar
