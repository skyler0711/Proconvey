import { CircleTickIcon, WarningIcon } from '@proconvey/ui/src/icons'
import { Toaster } from 'react-hot-toast'

const CustomToaster = () => (
  <Toaster
    toastOptions={{
      duration: 3000,
      className: 'align-child-left',
      style: {
        borderRadius: '8px',
        paddingTop: '16px',
        paddingBottom: '16px',
        paddingLeft: '34px',
        paddingRight: '34px',
        fontSize: '16px',
        fontWeight: 'medium',
        width: '100%',
        cursor: 'pointer',
      },
      success: {
        style: {
          border: '1px solid #62C0C1',
          color: '#62C0C1',
          backgroundColor: '#EFF9F9',
          width: '100%',
        },
        icon: <CircleTickIcon className="w-5 h-5 text-mint" />,
      },
      error: {
        style: {
          border: '1px solid #E21219',
          color: '#E21219',
          backgroundColor: '#FDF3F3',
        },
        icon: <WarningIcon className="w-5 h-5 text-danger" />,
      },
    }}
  />
)

export default CustomToaster
