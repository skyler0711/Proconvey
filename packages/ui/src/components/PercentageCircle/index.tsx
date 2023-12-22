import classNames from 'classnames'
import TickIcon from '../../icons/TickIcon'

type PropTypes = {
  percentage: number
  height?: number
  width?: number
  strokeWidth?: number
  fontSize?: number,
  innerCircleClassNames?: string
}

const PercentageCircle = ({
  percentage = 0,
  height = 66,
  width = 66,
  strokeWidth = 6,
  fontSize = 12,
  innerCircleClassNames = 'h-2/3 w-2/3',
}: PropTypes) => {

  const cx = width / 2
  const cy = height / 2
  const radius = cx - (strokeWidth / 2)

  const circumference = radius * 2 * Math.PI
  const offset = (circumference - percentage / 100 * circumference)

  const percentageClass = classNames(
    'absolute flex items-center justify-center bg-primary border-primary border border-opacity-[15%] rounded-full font-bold',
    innerCircleClassNames,
    {
      'bg-opacity-10': percentage !== 100,
    },
  )

  return (
    <div className="relative inline-flex items-center justify-center">

      <span className={percentageClass} style={{ fontSize: fontSize + 'px' }}>
        {
          percentage === 100
            ? <TickIcon className="text-base text-white" />
            : `${percentage?.toFixed(0)}%`
        }
      </span>

      <svg
        height={height}
        width={width}
        className="-rotate-90"
      >
        <circle
          className="stroke-chalk"
          strokeWidth={strokeWidth}
          strokeLinecap="round"
          fill="transparent"
          r={radius}
          cx={cx}
          cy={cy}
        />
        <circle
          className="transition-all progress-ring stroke-primary"
          strokeWidth={strokeWidth}
          strokeLinecap="round"
          strokeDasharray={`${circumference} ${circumference}`}
          fill="transparent"
          r={radius}
          cx={cx}
          cy={cy}
          style={{
            strokeDashoffset: offset,
          }}
        />
      </svg>
    </div>
  )
}

export default PercentageCircle
