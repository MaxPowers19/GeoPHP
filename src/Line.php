<?php

namespace Genarito\GeoPHP;

use \Genarito\GeoPHP\Geometry;
use \Genarito\GeoPHP\Point;

/**
 * Represents a line segment made up of exactly two Points.
 */
class Line implements Geometry {
    private $start;
    private $end;

    public function __construct(Point $start, Point $end) {
        $this->start = $start;
        $this->end = $end;
    }

    /**
     * Getter of the starting point of the line
     * @return Point starting point of the line
     */
    public function getStart() {
        return $this->start;
    }

    /**
     * Setter of the starting point of the line
     * @param Point $start starting point of the line
     * @return Line Instance
     */
    public function setStart(Point $start) {
        $this->start = $start;
        return $this;
    }

    /**
     * Getter of the ending point of the line
     * @return Point ending point of the line
     */
    public function getEnd() {
        return $this->end;
    }

    /**
     * Setter of the ending point of the line
     * @param Point $end ending point of the line
     * @return Line Instance
     */
    public function setEnd(Point $end) {
        $this->end = $end;
        return $this;
    }

    /**
     * Calculates the determinant of the line:
     * line.start.x * line.end.y - line.start.y * line.end.x
     * @return float|double The determinant of the line
     */
    public function determinant() {
        return $this->start->getX() * $this->end->getY() - $this->start->getY() * $this->end->getX();
    }

    /**
     * Calculates the difference in 'x' components (Δx):
     * line.end.x - line.start.x
     * @return float|double The difference in 'x' components (Δx)
     */
    public function dx() {
        return $this->end->getX() - $this->start->getX();
    }

    /**
     * Calculates the difference in 'y' components (Δy):
     * line.end.y - line.start.y
     * @return float|double The difference in 'y' components (Δy)
     */
    public function dy() {
        return $this->end->getY() - $this->start->getY();
    }

    /**
     * Calculates the slope of a line:
     * line.dy() / line.dx()
     * @return float|double The slope of a line
     */
    public function slope() {
        $dx = $this->dx();
        return ($dx != 0) ? $this->dy() / $dx : INF;
    }

    /**
     * Getter for starting and ending points
     * @return float|double[] Array with the starting point (0) and the ending point (1)
     */
    public function points() {
        return [$this->start, $this->end];
    }

    /**
     * Checks whether the line intersects a point
     * @param Point $point Point to check
     * @return True if the line intersects with the point, false otherwise
     */
    public function intersectsPoint(Point $point) {
        $dx = $this->dx();
        $tx = ($dx != 0) ? ($point->getX() - $this->start->getX()) / $this->dx() : 0;

        $dy = $this->dy();
        $ty = ($dy != 0) ? ($point->getY() - $this->start->getY()) / $this->dy() : 0;

        if (!$tx && !$ty) {
            return $point->isEqual($this->start);
        }

        if ($tx && !$ty) {
            return $point->getY() == $this->start->getY() && 0 <= $tx && $tx <= 1;
        }

        if (!$tx && $ty) {
            return $point->getX() == $this->start->getX() && 0 <= $ty && $ty <= 1;
        }

        // If $tx and $ty...
        return abs($tx - $ty) <= 0.000001 && 0 <= $tx && $tx <= 1;
    }

    /**
     * Checks whether the line intersects another line
     * @param Line $line Line to check
     * @return True if the line intersects with the point, false otherwise
     */
    public function intersectLine(Line $line) {
        // Using Cramer's Rule:
        // https://en.wikipedia.org/wiki/Intersection_%28Euclidean_geometry%29#Two_line_segments
        $a1 = $this->dx();
        $a2 = $this->dy();
        $b1 = -$line->dx();
        $b2 = -$line->dy();
        $c1 = $line->start->getX() - $this->start->getX();
        $c2 = $line->start->getY() - $this->start->getY();

        $d = $a1 * $b2 - $a2 * $b1;
        if (!$d) {
            $thisPoints = $this->points();
            $selfStart = $thisPoints[0];
            $selfEnd = $thisPoints[1];
            $otherPoints = $line->points();
            $otherStart = $otherPoints[0];
            $otherEnd = $otherPoints[1];

            // Lines are parallel
            // Return true if at least one end point intersects the other line
            return $selfStart->intersects($line)
                || $selfEnd->intersects($line)
                || $otherStart->intersects($this)
                || $otherEnd->intersects($this);
        }

        $s = ($c1 * $b2 - $c2 * $b1) / $d;
        $t = ($a1 * $c2 - $a2 * $c1) / $d;
        return (0 <= $s) && ($s <= 1) && (0 <= $t) && ($t <= 1);
    }

    /**
     * Abstract method implementation
     */
    public function area() {
        return 0;
    }

    /**
     * Abstract method implementation
     */
    public function intersects(Geometry $otherGeometry): bool {
        $class = get_class($otherGeometry);
        switch ($class) {
            case Point::class:
                $intersects = $this->intersectsPoint($otherGeometry);
                break;
            case Line::class:
                $intersects = $this->intersectLine($otherGeometry);
                break;
            default:
                throw new Exception("Not valid geometry", 1);
                break;
        }

        return $intersects;
    }
}